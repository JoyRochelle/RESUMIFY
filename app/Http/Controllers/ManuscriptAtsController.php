<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;

class ManuscriptAtsController extends Controller
{
    /**
     * Score a CV's content using Gemini and return an ATS score (0-100).
     * This is called automatically after each section save in the manuscript editor.
     */
    public function score(Request $request, Cv $cv): JsonResponse
    {
        \Illuminate\Support\Facades\Gate::authorize('view', $cv);

        $apiKey = config('services.gemini.key');
        if (!$apiKey) {
            return response()->json(['error' => 'Gemini API key not configured.'], 500);
        }

        // Flatten the CV content into readable text for analysis
        $cv->load('sections');
        $resumeText = $this->flattenCv($cv);
        
        $targetJob = $cv->sections->where('type', 'target_job')->first();
        $jobTitle = $targetJob->content['job_title'] ?? null;
        $jobDescription = $targetJob->content['job_description'] ?? null;

        if (strlen(trim($resumeText)) < 30) {
            return response()->json(['score' => 0, 'tip' => 'Add more content to get your ATS score.']);
        }

        $prompt = $this->buildPrompt($resumeText, $jobTitle, $jobDescription);

        try {
            set_time_limit(60);

            $response = Http::timeout(25)
                ->connectTimeout(5)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key={$apiKey}", [
                    'contents' => [[
                        'parts' => [['text' => $prompt]],
                    ]],
                    'generationConfig' => [
                        'response_mime_type' => 'application/json',
                    ],
                ]);

            if ($response->failed()) {
                Log::error('ManuscriptAts Gemini Error', [
                    'status'   => $response->status(),
                    'body'     => $response->body(),
                ]);
                return response()->json(['error' => 'AI service unavailable.'], 502);
            }

            $result  = $response->json();
            $content = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$content) {
                return response()->json(['error' => 'Empty response from AI.'], 500);
            }

            $data = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE || !isset($data['score'])) {
                return response()->json(['error' => 'Could not parse AI response.'], 500);
            }

            // Clamp score to 0-100
            $data['score'] = max(0, min(100, (int) $data['score']));

            return response()->json($data);

        } catch (ConnectionException $e) {
            Log::error('ManuscriptAts Timeout', ['msg' => $e->getMessage()]);
            return response()->json(['error' => 'AI service timed out.'], 504);
        } catch (\Exception $e) {
            Log::error('ManuscriptAts Exception', ['msg' => $e->getMessage()]);
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    /**
     * Convert CV sections to plain text for the AI prompt.
     */
    private function flattenCv(Cv $cv): string
    {
        $lines = [];

        foreach ($cv->sections->sortBy('order') as $section) {
            // Exclude target_job from the resume text itself (it's the context, not the content)
            if ($section->type === 'target_job') continue;
            
            $content = $section->content;
            if (empty($content)) continue;

            $lines[] = strtoupper($section->title) . ':';

            // Check if it's a list (array of objects) or a flat object
            if (isset($content[0]) && is_array($content[0])) {
                // List-based (experience, education, skills)
                foreach ($content as $item) {
                    if (is_array($item)) {
                        $lines[] = '• ' . implode(' | ', array_filter(array_values($item)));
                    }
                }
            } else {
                // Flat object (personal_info, etc.)
                foreach ($content as $key => $value) {
                    if (!empty($value) && is_string($value)) {
                        $lines[] = ucfirst(str_replace('_', ' ', $key)) . ': ' . $value;
                    }
                }
            }

            $lines[] = '';
        }

        return implode("\n", $lines);
    }

    private function buildPrompt(string $resumeText, ?string $jobTitle = null, ?string $jobDescription = null): string
    {
        $jobContext = "";
        if ($jobTitle || $jobDescription) {
            $jobContext = "Evaluate this resume against the following job:\n";
            if ($jobTitle) $jobContext .= "JOB TITLE: $jobTitle\n";
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
