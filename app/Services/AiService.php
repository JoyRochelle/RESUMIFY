<?php

namespace App\Services;

use App\Domain\Ai\Data\AtsAnalysisResponse;
use App\Domain\Ai\Data\CvVersionsResponse;
use App\Domain\Ai\Data\ResumeBulletOptionsResponse;
use App\Exceptions\InvalidAiProviderResponseException;
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

        return $this->callGeminiValidated(
            $prompt,
            [ResumeBulletOptionsResponse::class, 'fromProvider'],
            self::resumeBulletOptionsSchema(),
        );
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

        $prompts = [];
        foreach ($angles as $angle => $instruction) {
            $prompt = "You are an expert ATS resume writer. Rewrite the provided resume sections to tailor them for the given job description.\n";
            $prompt .= "ANGLE TO FOCUS ON: {$instruction}\n\n";
            $prompt .= "JOB DESCRIPTION:\n{$jobDescription}\n\n";
            $prompt .= "CURRENT RESUME JSON (Array of sections):\n" . json_encode($currentSections) . "\n\n";
            $prompt .= "Return ONLY the modified JSON array representing the new resume sections. DO NOT change the structure, just update the text values in descriptions/bullets to fit the angle. MUST return a valid JSON array.";

            $prompts[$angle] = $prompt;
        }

        $responses = Http::pool(function (Pool $pool) use ($apiKey, $angles, $prompts) {
            $reqs = [];
            foreach ($angles as $angle => $instruction) {
                $reqs[] = $pool->as($angle)->timeout(60)->post(self::GEMINI_URL . "?key={$apiKey}", [
                    'contents' => [['parts' => [['text' => $prompts[$angle]]]]],
                    'generationConfig' => self::jsonGenerationConfig(self::cvVersionsSchema()),
                ]);
            }
            return $reqs;
        });

        $results = [];
        foreach ($angles as $angle => $instruction) {
            $response = $responses[$angle];
            if ($response instanceof \Illuminate\Http\Client\Response && $response->ok()) {
                $result = $response->json();
                $content = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if (!$content) {
                    throw new \Exception("Empty response from AI service for {$angle} CV version.");
                }

                $results[$angle] = $this->decodeValidateOrRepair(
                    $prompts[$angle],
                    $content,
                    self::cvVersionsSchema(),
                    [CvVersionsResponse::class, 'fromProvider'],
                    60,
                );
            } else {
                Log::error("AiService generateCvVersions failed for angle {$angle}");
                throw new \Exception("AI service failed for {$angle} CV version.");
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
        return $this->callGeminiValidated(
            $prompt,
            [AtsAnalysisResponse::class, 'fromProvider'],
            self::atsAnalysisSchema(),
            30,
        );
    }

    /**
     * Score a resume for ATS compatibility (lightweight scoring for manuscript editor).
     * Returns score, label, tip, strengths, and improvements.
     */
    public function scoreResume(string $resumeText, ?string $jobTitle = null, ?string $jobCompany = null, ?string $jobDescription = null): array
    {
        $prompt = $this->buildScorePrompt($resumeText, $jobTitle, $jobCompany, $jobDescription);
        return $this->callGeminiJson($prompt, self::scoreSchema(), 25);
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
     * Make a single call to the Gemini API and return a validated JSON result.
     *
     * @throws \Exception if the API key is missing, the call fails, or the response is empty.
     */
    private function callGeminiValidated(string $prompt, callable $validator, array $schema, int $timeout = 30): array
    {
        $content = $this->requestGeminiText($prompt, $schema, $timeout);

        return $this->decodeValidateOrRepair($prompt, $content, $schema, $validator, $timeout);
    }

    /**
     * Make a single call to the Gemini API and return the parsed JSON result.
     *
     * @throws \Exception if the API key is missing, the call fails, or the response is empty.
     */
    private function callGeminiJson(string $prompt, array $schema, int $timeout = 30): array
    {
        $content = $this->requestGeminiText($prompt, $schema, $timeout);

        return $this->decodeValidateOrRepair($prompt, $content, $schema, null, $timeout);
    }

    private function requestGeminiText(string $prompt, array $schema, int $timeout = 30): string
    {
        $apiKey = $this->getApiKey();

        $response = Http::timeout($timeout)->connectTimeout(5)->post(self::GEMINI_URL . "?key={$apiKey}", [
            'contents' => [['parts' => [['text' => $prompt]]]],
            'generationConfig' => self::jsonGenerationConfig($schema),
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

        return $content;
    }

    private function decodeValidateOrRepair(
        string $prompt,
        string $content,
        array $schema,
        ?callable $validator,
        int $timeout,
    ): array {
        try {
            return $this->decodeAndValidate($content, $validator);
        } catch (InvalidAiProviderResponseException $e) {
            Log::warning('AiService Gemini JSON response invalid; retrying repair once', [
                'error' => $e->getMessage(),
            ]);

            $repairContent = $this->requestGeminiText(
                $this->buildRepairPrompt($prompt, $content, $e->getMessage()),
                $schema,
                $timeout,
            );

            return $this->decodeAndValidate($repairContent, $validator);
        }
    }

    private function decodeAndValidate(string $content, ?callable $validator = null): array
    {
        // Clean markdown code blocks if present
        $content = preg_replace('/^```json\s*|\s*```$/i', '', trim($content));

        $decoded = json_decode($content, true);
        if (!is_array($decoded) || json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidAiProviderResponseException('Invalid JSON from AI service: ' . json_last_error_msg());
        }

        if ($validator) {
            return $validator($decoded);
        }

        return $decoded;
    }

    private function buildRepairPrompt(string $originalPrompt, string $invalidJson, string $error): string
    {
        return <<<PROMPT
The previous response did not satisfy the required JSON contract.

Validation error:
{$error}

Original task:
{$originalPrompt}

Invalid response:
{$invalidJson}

Return ONLY corrected valid JSON that satisfies the original task and schema. Do not include markdown or explanation.
PROMPT;
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

    private static function jsonGenerationConfig(array $schema): array
    {
        return [
            'response_mime_type' => 'application/json',
            'response_schema' => $schema,
        ];
    }

    private static function resumeBulletOptionsSchema(): array
    {
        return [
            'type' => 'array',
            'minItems' => 3,
            'maxItems' => 3,
            'items' => [
                'type' => 'string',
                'maxLength' => 500,
            ],
        ];
    }

    private static function cvVersionsSchema(): array
    {
        return [
            'type' => 'array',
            'maxItems' => 30,
            'items' => [
                'type' => 'object',
                'properties' => [
                    'type' => [
                        'type' => 'string',
                        'enum' => [
                            'personal_info',
                            'work_experience',
                            'education',
                            'skills',
                            'target_job',
                            'certifications',
                            'projects',
                            'languages',
                        ],
                    ],
                    'title' => ['type' => 'string', 'maxLength' => 100],
                    'content' => [
                        'anyOf' => [
                            [
                                'type' => 'object',
                                'additionalProperties' => true,
                            ],
                            [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'object',
                                    'additionalProperties' => true,
                                ],
                            ],
                        ],
                    ],
                ],
                'required' => ['type', 'title', 'content'],
            ],
        ];
    }

    private static function atsAnalysisSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'score' => ['type' => 'integer', 'minimum' => 0, 'maximum' => 100],
                'rating' => [
                    'type' => 'object',
                    'properties' => [
                        'label' => ['type' => 'string', 'enum' => ['Excellent', 'Very Good', 'Fair', 'Weak']],
                        'sublabel' => ['type' => 'string', 'enum' => ['ATS-Ready', 'Needs Minor Polish', 'Room to Improve', 'Needs Rework']],
                        'color' => ['type' => 'string', 'enum' => ['success', 'warning', 'danger']],
                    ],
                    'required' => ['label', 'sublabel', 'color'],
                ],
                'keyword_score' => ['type' => 'integer', 'minimum' => 0, 'maximum' => 100],
                'matched' => ['type' => 'array', 'maxItems' => 15, 'items' => ['type' => 'string']],
                'missing' => [
                    'type' => 'array',
                    'maxItems' => 20,
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'keyword' => ['type' => 'string', 'maxLength' => 100],
                            'context' => ['type' => 'string', 'maxLength' => 500],
                        ],
                        'required' => ['keyword', 'context'],
                    ],
                ],
                'section_breakdown' => [
                    'type' => 'array',
                    'maxItems' => 12,
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'section' => ['type' => 'string', 'maxLength' => 100],
                            'strength' => ['type' => 'string', 'enum' => ['Strong', 'Adequate', 'Weak']],
                            'feedback' => ['type' => 'string', 'maxLength' => 700],
                        ],
                        'required' => ['section', 'strength', 'feedback'],
                    ],
                ],
                'action_verbs' => ['type' => 'array', 'maxItems' => 20, 'items' => ['type' => 'string']],
                'missing_verbs' => ['type' => 'array', 'maxItems' => 10, 'items' => ['type' => 'string']],
                'has_numbers' => ['type' => 'boolean'],
                'length_tip' => ['type' => 'string', 'maxLength' => 500],
                'insights' => [
                    'type' => 'array',
                    'minItems' => 4,
                    'maxItems' => 4,
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'title' => ['type' => 'string', 'maxLength' => 100],
                            'body' => ['type' => 'string', 'maxLength' => 700],
                        ],
                        'required' => ['title', 'body'],
                    ],
                ],
            ],
            'required' => [
                'score',
                'rating',
                'keyword_score',
                'matched',
                'missing',
                'section_breakdown',
                'action_verbs',
                'missing_verbs',
                'has_numbers',
                'length_tip',
                'insights',
            ],
        ];
    }

    private static function scoreSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'score' => ['type' => 'integer', 'minimum' => 0, 'maximum' => 100],
                'label' => ['type' => 'string', 'enum' => ['Excellent', 'Very Good', 'Fair', 'Weak']],
                'tip' => ['type' => 'string'],
                'strengths' => ['type' => 'array', 'maxItems' => 3, 'items' => ['type' => 'string']],
                'improvements' => ['type' => 'array', 'maxItems' => 3, 'items' => ['type' => 'string']],
            ],
            'required' => ['score', 'label', 'tip', 'strengths', 'improvements'],
        ];
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
