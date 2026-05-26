<?php

namespace App\Http\Controllers;

use App\Services\AiService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AtsController extends Controller
{
    protected AiService $aiService;

    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Analyze resume against job description using Gemini AI.
     * Premium feature — guarded by ai.quota middleware in routes.
     */
    public function analyze(Request $request)
    {
        $request->validate([
            'resume'          => ['required', 'string', 'min:50', 'max:20000'],
            'job_description' => ['required', 'string', 'min:50', 'max:20000'],
        ]);

        $resumeText = $request->input('resume');
        $jdText     = $request->input('job_description');
        $user       = $request->user();

        // Deduct credit BEFORE the AI call
        $user->increment('ai_quota_used', 1);

        try {
            $analysis = $this->aiService->analyzeAts($resumeText, $jdText);

            // Add word count (client-side display)
            $analysis['word_count'] = str_word_count($resumeText);

            // Log usage
            $this->aiService->logUsage($user->id, 'ats_analyze');

            return response()->json($analysis);

        } catch (ConnectionException $e) {
            // Refund the credit on timeout
            $user->decrement('ai_quota_used', 1);
            Log::error('ATS Connection Timeout', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'The AI service did not respond in time. Please try again.'], 504);

        } catch (\Exception $e) {
            // Refund the credit on failure
            $user->decrement('ai_quota_used', 1);
            Log::error('ATS Analysis Exception', ['message' => $e->getMessage()]);
            return response()->json(['message' => 'An error occurred during analysis.'], 500);
        }
    }
}
