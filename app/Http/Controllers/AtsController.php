<?php

namespace App\Http\Controllers;

use App\Actions\Ai\RunAtsAnalysisAction;
use App\Exceptions\AiQuotaExceededException;
use App\Http\Requests\AnalyzeAtsRequest;
use App\Models\AtsScan;
use App\Support\ApiResponse;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AtsController extends Controller
{
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
    public function analyze(AnalyzeAtsRequest $request, RunAtsAnalysisAction $runAtsAnalysis): JsonResponse
    {
        $user = $request->user();

        if (!$user->canUsePremiumFeature('ats_analyze')) {
            return ApiResponse::legacyError(
                legacyCode: 'premium_required',
                message: 'Upgrade to Premium to unlock full ATS analysis.',
                status: 402,
                legacy: ['upgrade_url' => route('user.upgrade-quota')],
                standardCode: ApiResponse::PREMIUM_REQUIRED,
            );
        }

        abort_if(
            $request->filled('cv_id') && !$user->cvs()->whereKey($request->input('cv_id'))->exists(),
            404
        );

        try {
            return ApiResponse::success($runAtsAnalysis->execute($user, $request->validated()));
        } catch (AiQuotaExceededException $e) {
            return ApiResponse::error(
                code: ApiResponse::QUOTA_EXCEEDED,
                message: $e->getMessage(),
                details: [
                    'remaining' => $e->remainingCredits,
                    'limit' => $e->quotaLimit,
                ],
                status: 402,
                legacy: [
                    'remaining' => $e->remainingCredits,
                    'limit' => $e->quotaLimit,
                ],
            );
        } catch (ConnectionException $e) {
            return ApiResponse::error(
                code: ApiResponse::AI_PROVIDER_TIMEOUT,
                message: 'The AI service did not respond in time. Please try again.',
                status: 504,
            );
        } catch (\Exception $e) {
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
