<?php

namespace App\Http\Controllers;

use App\Models\AtsScan;
use App\Models\Cv;
use App\Services\AiCreditService;
use App\Services\AiService;
use App\Support\ApiResponse;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AtsController extends Controller
{
    protected AiService $aiService;
    protected AiCreditService $aiCreditService;

    public function __construct(AiService $aiService, AiCreditService $aiCreditService)
    {
        $this->aiService = $aiService;
        $this->aiCreditService = $aiCreditService;
    }

    /**
     * Show the ATS Analyzer page with CVs and scan history.
     */
    public function index(): View
    {
        $user = auth()->user();
        $cvs  = $user->cvs()->with('sections')->latest()->get();

        $history = $user->atsScans()
            ->with('cv:id,title')
            ->select(['id', 'cv_id', 'job_title', 'job_company', 'score', 'created_at'])
            ->limit(20)
            ->get();

        $quota = [
            'used'       => $user->ai_quota_used,
            'limit'      => $user->getQuotaLimit(),
            'remaining'  => $user->getQuotaRemaining(),
            'percentage' => $user->getQuotaPercentage(),
        ];

        return view('user.ai-assistant', compact('cvs', 'history', 'quota'));
    }

    /**
     * Return the full result JSON of a single past scan (for history card re-render).
     */
    public function showHistory(AtsScan $scan): JsonResponse
    {
        abort_if($scan->user_id !== auth()->id(), 403);
        return response()->json($scan->result_json ?? []);
    }

    /**
     * Analyze resume against job description using Gemini AI.
     * Premium feature — guarded by ai.quota middleware in routes.
     */
    public function analyze(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->canUsePremiumFeature('ats_analyze')) {
            return response()->json([
                'error' => 'premium_required',
                'message' => 'Upgrade to Premium to unlock full ATS analysis.',
                'upgrade_url' => route('user.upgrade-quota'),
            ], 402);
        }

        $request->validate([
            'resume'          => ['required', 'string', 'min:50', 'max:20000'],
            'job_description' => ['required', 'string', 'min:50', 'max:20000'],
            'cv_id'           => ['nullable', 'string', 'exists:cvs,id'],
            'job_title'       => ['nullable', 'string', 'max:255'],
            'job_company'     => ['nullable', 'string', 'max:255'],
        ]);

        abort_if(
            $request->filled('cv_id') && !$user->cvs()->whereKey($request->input('cv_id'))->exists(),
            404
        );

        $resumeText = $request->input('resume');
        $jdText     = $request->input('job_description');

        $reservation = $this->aiCreditService->reserve($user, 1, 'ats_analyze');

        if ($reservation->isDenied()) {
            return ApiResponse::error(
                code: ApiResponse::QUOTA_EXCEEDED,
                message: 'You have used all your AI credits. Upgrade to Premium for 50 credits/month.',
                details: [
                    'remaining' => $reservation->remainingCredits(),
                    'limit' => $reservation->quotaLimit,
                ],
                status: 402,
                legacy: [
                    'remaining' => $reservation->remainingCredits(),
                    'limit' => $reservation->quotaLimit,
                ],
            );
        }

        try {
            $analysis = $this->aiService->analyzeAts($resumeText, $jdText);

            // Add word count (client-side display)
            $analysis['word_count'] = str_word_count($resumeText);

            // Persist the result so user can load it again from history
            $scan = AtsScan::create([
                'user_id'         => $user->id,
                'cv_id'           => $request->input('cv_id') ?: null,
                'job_title'       => $request->input('job_title') ?: null,
                'job_company'     => $request->input('job_company') ?: null,
                'job_description' => $jdText,
                'score'           => $analysis['score'] ?? null,
                'matched_keywords'=> $analysis['matched'] ?? null,
                'result_json'     => $analysis,
            ]);

            // Log usage
            $this->aiService->logUsage($user->id, 'ats_analyze');

            // Include scan meta so frontend can prepend to history list
            $analysis['_scan_id']      = $scan->id;
            $analysis['_scan_created'] = $scan->created_at->toISOString();

            return ApiResponse::success($analysis);

        } catch (ConnectionException $e) {
            $this->aiCreditService->refund($reservation);
            Log::error('ATS Connection Timeout', ['message' => $e->getMessage()]);
            return ApiResponse::error(
                code: ApiResponse::AI_PROVIDER_TIMEOUT,
                message: 'The AI service did not respond in time. Please try again.',
                status: 504,
            );

        } catch (\Exception $e) {
            $this->aiCreditService->refund($reservation);
            Log::error('ATS Analysis Exception', ['message' => $e->getMessage()]);
            return ApiResponse::error(
                code: ApiResponse::AI_PROVIDER_INVALID_RESPONSE,
                message: 'An error occurred during analysis.',
                status: 500,
            );
        }
    }

    /**
     * Delete a single history record owned by the authenticated user.
     */
    public function destroyHistory(AtsScan $scan): JsonResponse
    {
        abort_if($scan->user_id !== auth()->id(), 403);
        $scan->delete();
        return response()->json(['success' => true]);
    }
}
