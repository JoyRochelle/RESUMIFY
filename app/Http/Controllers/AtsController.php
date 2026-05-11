<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AtsController extends Controller
{
    /**
     * Analyze resume against job description using Gemini AI.
     */
    public function analyze(Request $request)
    {
        $request->validate([
            'resume'          => ['required', 'string', 'min:50', 'max:20000'],
            'job_description' => ['required', 'string', 'min:50', 'max:20000'],
        ]);

        $resumeText = $request->input('resume');
        $jdText     = $request->input('job_description');
        $apiKey     = config('services.gemini.key');

        if (!$apiKey) {
            return response()->json([
                'message' => 'Gemini API key is not configured. Please add GEMINI_API_KEY to your .env file.'
            ], 500);
        }

        $prompt = $this->buildPrompt($resumeText, $jdText);

        try {
            $response = Http::timeout(60)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-lite-latest:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'response_mime_type' => 'application/json',
                ]
            ]);

            if ($response->failed()) {
                Log::error('Gemini API Error', [
                    'status'   => $response->status(),
                    'response' => $response->body(),
                ]);
                $errorBody = $response->json();
                $apiMsg    = $errorBody['error']['message'] ?? 'Failed to connect to the AI service.';
                return response()->json(['message' => $apiMsg], 502);
            }

            $result = $response->json();
            $content = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$content) {
                return response()->json(['message' => 'Invalid response from AI service.'], 500);
            }

            $analysis = json_decode($content, true);

            $analysis['word_count'] = str_word_count($resumeText);

            return response()->json($analysis);

        } catch (\Exception $e) {
            Log::error('ATS Analysis Exception', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'An error occurred during analysis.'], 500);
        }
    }

    private function buildPrompt(string $resume, string $jd): string
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
  "missing": (array of strings: top 10 missing critical keywords from JD),
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
}
