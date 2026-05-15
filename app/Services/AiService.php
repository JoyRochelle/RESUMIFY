<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Pool;

class AiService
{
    /**
     * Refine a resume bullet point.
     * Uses 1 AI credit (handled in controller).
     */
    public function refineBullet(string $text, string $jobContext = null): array
    {
        $apiKey = config('services.gemini.key');
        if (!$apiKey) {
            throw new \Exception("Gemini API key not configured.");
        }

        $prompt = "You are an expert resume writer. Rewrite the following resume bullet point to make it more impactful using strong action verbs and quantifying results where plausible. Maintain the user's authentic voice.\n\n";
        if ($jobContext) {
            $prompt .= "Context (Job Description / Title):\n{$jobContext}\n\n";
        }
        $prompt .= "Original Bullet:\n{$text}\n\n";
        $prompt .= "Return ONLY a JSON array of strings containing exactly 3 alternative rewrites.";

        $response = Http::timeout(30)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key={$apiKey}", [
            'contents' => [['parts' => [['text' => $prompt]]]],
            'generationConfig' => ['response_mime_type' => 'application/json'],
        ]);

        if ($response->failed()) {
            Log::error('AiService refineBullet failed', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \Exception("AI service failed");
        }

        $result = $response->json();
        $content = $result['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
        
        // Clean markdown block if present
        $content = preg_replace('/^```json\s*|\s*```$/i', '', trim($content));
        
        return json_decode($content, true) ?: [];
    }

    /**
     * Generate 3 CV versions in parallel.
     * Uses 3 AI credits (handled in controller).
     */
    public function generateCvVersions(array $currentSections, string $jobDescription): array
    {
        $apiKey = config('services.gemini.key');
        if (!$apiKey) {
            throw new \Exception("Gemini API key not configured.");
        }
        
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

                $reqs[] = $pool->as($angle)->timeout(60)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key={$apiKey}", [
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
}
