<?php

namespace App\Services;

use App\Models\AiUsageLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Pool;

class AiService
{
    /**
     * The Gemini API endpoint (model + action).
     */
    private const GEMINI_URL = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

    /**
     * Refine a resume bullet point.
     * Returns an array of 3 alternative rewrites.
     */
    public function refineBullet(string $text, string $jobContext = null): array
    {
        $prompt = "You are an expert resume writer. Rewrite the following resume bullet point to make it more impactful using strong action verbs and quantifying results where plausible. Maintain the user's authentic voice.\n\n";
        if ($jobContext) {
            $prompt .= "Context (Job Description / Title):\n{$jobContext}\n\n";
        }
        $prompt .= "Original Bullet:\n{$text}\n\n";
        $prompt .= "Return ONLY a JSON array of strings containing exactly 3 alternative rewrites.";

        return $this->callGemini($prompt);
    }

    /**
     * Generate 3 CV versions in parallel using Http::pool().
     * Each version targets a different angle (leadership, technical, ownership).
     */
    public function generateCvVersions(array $currentSections, string $jobDescription): array
    {
        $apiKey = $this->getApiKey();
        
        $angles = [
            'leadership' => "Focus on team leadership, project ownership, cross-functional collaboration, and decision-making.",
            'technical' => "Focus on specific tools, technologies, methodologies, technical achievements, and certifications.",
            'ownership' => "Focus on end-to-end responsibility, initiative, startup mindset, autonomy, and measurable business impact."
        ];

        $responses = Http::pool(function (Pool $pool) use ($apiKey, $angles, $currentSections, $jobDescription) {
            $reqs = [];
            foreach ($angles as $angle => $instruction) {
                $prompt = "You are an expert ATS resume writer. Rewrite the provided resume sections to tailor them for the given job description.\n";
                $prompt .= "ANGLE TO FOCUS ON: {$instruction}\n\n";
                $prompt .= "JOB DESCRIPTION:\n{$jobDescription}\n\n";
                $prompt .= "CURRENT RESUME JSON (Array of sections):\n" . json_encode($currentSections) . "\n\n";
                $prompt .= "Return ONLY the modified JSON array representing the new resume sections. DO NOT change the structure, just update the text values in descriptions/bullets to fit the angle. MUST return a valid JSON array.";

                $reqs[] = $pool->as($angle)->timeout(60)->post(self::GEMINI_URL . "?key={$apiKey}", [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['response_mime_type' => 'application/json'],
                ]);
            }
            return $reqs;
        });

        $results = [];
        foreach ($angles as $angle => $instruction) {
            $response = $responses[$angle];
            if ($response instanceof \Illuminate\Http\Client\Response && $response->ok()) {
                $result = $response->json();
                $content = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'null';
                
                // Clean markdown block if present
                $content = preg_replace('/^```json\s*|\s*```$/i', '', trim($content));
                
                $results[$angle] = json_decode($content, true) ?: $currentSections;
            } else {
                Log::error("AiService generateCvVersions failed for angle {$angle}");
                $results[$angle] = $currentSections; // Fallback to original
            }
        }

        return $results;
    }

    /**
     * Analyze a resume against a job description (full ATS analysis).
     * Returns the complete analysis object with score, keywords, insights, etc.
     */
    public function analyzeAts(string $resumeText, string $jobDescription): array
    {
        $prompt = $this->buildAtsAnalysisPrompt($resumeText, $jobDescription);
        return $this->callGemini($prompt, 30);
    }

    /**
     * Score a resume for ATS compatibility (lightweight scoring for manuscript editor).
     * Returns score, label, tip, strengths, and improvements.
     */
    public function scoreResume(string $resumeText, ?string $jobTitle = null, ?string $jobCompany = null, ?string $jobDescription = null): array
    {
        $prompt = $this->buildScorePrompt($resumeText, $jobTitle, $jobCompany, $jobDescription);
        return $this->callGemini($prompt, 25);
    }

    /**
     * Log an AI usage event to ai_usage_logs.
     */
    public function logUsage(string $userId, string $actionType, ?string $resumeId = null, int $tokensUsed = 0): void
    {
        try {
            AiUsageLog::create([
                'user_id'     => $userId,
                'action_type' => $actionType,
                'tokens_used' => $tokensUsed,
                'cost_usd'    => 0,
                'resume_id'   => $resumeId,
            ]);
        } catch (\Exception $e) {
            // Logging should never break the main flow
            Log::warning('Failed to log AI usage', ['error' => $e->getMessage()]);
        }
    }

    // ─── Private Helpers ──────────────────────────────────────

    /**
     * Make a single call to the Gemini API and return the parsed JSON result.
     *
     * @throws \Exception if the API key is missing, the call fails, or the response is empty.
     */
    private function callGemini(string $prompt, int $timeout = 30): array
    {
        $apiKey = $this->getApiKey();

        $response = Http::timeout($timeout)->connectTimeout(5)->post(self::GEMINI_URL . "?key={$apiKey}", [
            'contents' => [['parts' => [['text' => $prompt]]]],
            'generationConfig' => ['response_mime_type' => 'application/json'],
        ]);

        if ($response->failed()) {
            Log::error('AiService Gemini API failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \Exception('AI service failed with status ' . $response->status());
        }

        $content = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if (!$content) {
            throw new \Exception('Empty response from AI service');
        }

        // Clean markdown code blocks if present
        $content = preg_replace('/^```json\s*|\s*```$/i', '', trim($content));

        $decoded = json_decode($content, true);
        if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON from AI service: ' . json_last_error_msg());
        }

        return $decoded ?: [];
    }

    /**
     * Get and validate the Gemini API key.
     *
     * @throws \Exception if the key is not configured.
     */
    private function getApiKey(): string
    {
        $apiKey = config('services.gemini.key');
        if (!$apiKey) {
            throw new \Exception('Gemini API key not configured.');
        }
        return $apiKey;
    }

    /**
     * Build the full ATS analysis prompt (for AI Assistant page).
     */
    private function buildAtsAnalysisPrompt(string $resume, string $jd): string
    {
        return <<<PROMPT
You are an expert ATS (Applicant Tracking System) analyzer. 
Analyze the following RESUME against the JOB DESCRIPTION.

RESUME:
{$resume}

JOB DESCRIPTION:
{$jd}

Return a JSON object with exactly this structure:
{
  "score": (integer 0-100),
  "rating": {
    "label": (string: "Excellent", "Very Good", "Fair", or "Weak"),
    "sublabel": (string: "ATS-Ready", "Needs Minor Polish", "Room to Improve", or "Needs Rework"),
    "color": (string: "success" for Excellent/Very Good, "warning" for Fair, "danger" for Weak)
  },
  "keyword_score": (integer 0-100),
  "matched": (array of strings: top 15 matching keywords/skills),
  "missing": [
    {
      "keyword": (string: the missing keyword),
      "context": (string: short context on why this keyword matters for this role)
    }
  ],
  "section_breakdown": [
    {
      "section": (string: e.g. "Summary", "Experience", "Education", "Skills"),
      "strength": (string: "Strong", "Adequate", or "Weak"),
      "feedback": (string: short actionable feedback for this section)
    }
  ],
  "action_verbs": (array of strings: strong verbs found in resume),
  "missing_verbs": (array of strings: 4-6 recommended action verbs to add),
  "has_numbers": (boolean: true if resume contains metrics/numbers),
  "length_tip": (string: a short tip about the resume length),
  "insights": [
    {
      "title": (string: short catchy title),
      "body": (string: actionable advice)
    }
  ] (exactly 4 insights)
}

Be critical and professional. Ensure the JSON is valid.
PROMPT;
    }

    /**
     * Build the lightweight score prompt (for manuscript editor auto-score).
     */
    private function buildScorePrompt(string $resumeText, ?string $jobTitle = null, ?string $jobCompany = null, ?string $jobDescription = null): string
    {
        $jobContext = "";
        if ($jobTitle || $jobCompany || $jobDescription) {
            $jobContext = "Evaluate this resume against the following job:\n";
            if ($jobTitle) $jobContext .= "JOB TITLE: $jobTitle\n";
            if ($jobCompany) $jobContext .= "COMPANY: $jobCompany\n";
            if ($jobDescription) $jobContext .= "JOB DESCRIPTION: $jobDescription\n";
            $jobContext .= "\nFocus on: keyword matching, relevance of experience to this role, and overall fit.";
        } else {
            $jobContext = "Evaluate this resume for overall quality WITHOUT a specific job description. Focus on: completeness, quantifiable achievements, formatting, and keyword richness.";
        }

        return <<<PROMPT
You are a professional ATS (Applicant Tracking System) expert.

{$jobContext}

RESUME:
{$resumeText}

Return ONLY a valid JSON object with exactly this structure:
{
  "score": (integer 0-100),
  "label": (string: "Excellent" | "Very Good" | "Fair" | "Weak"),
  "tip": (string: one actionable sentence to improve the score),
  "strengths": (array of 2-3 short strings: what the resume does well),
  "improvements": (array of 2-3 short strings: what needs improvement)
}

Be critical but fair. Ensure the JSON is valid and complete.
PROMPT;
    }
}
