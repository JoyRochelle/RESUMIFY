<?php

namespace App\Http\Controllers\User\Ai;

use App\Actions\Ai\GenerateResumeVersionsAction;
use App\Actions\Ai\RefineResumeBulletAction;
use App\Actions\Resumes\ApplyChameleonAdaptationAction;
use App\Exceptions\AiQuotaExceededException;
use App\Exceptions\InsufficientResumeContentException;
use App\Exceptions\InvalidAiProviderResponseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateResumeVersionsRequest;
use App\Http\Requests\RefineResumeBulletRequest;
use App\Models\ChameleonAdaptation;
use App\Models\Cv;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class AiResumeController extends Controller
{
    /**
     * Refine a single resume bullet point using AI.
     * Premium gating + quota check handled by 'ai.quota' middleware on the route.
     */
    public function refineBullet(
        RefineResumeBulletRequest $request,
        Cv $cv,
        RefineResumeBulletAction $refineResumeBullet,
    ): JsonResponse {
        Gate::authorize('update', $cv);

        try {
            $user = $request->user();
            $options = $refineResumeBullet->execute(
                user: $user,
                cv: $cv,
                text: $request->validated('text'),
                jobContext: $request->validated('job_context'),
            );

            return ApiResponse::success(['options' => $options, 'quota' => $user->aiQuotaSnapshot()]);
        } catch (AiQuotaExceededException $e) {
            return $this->quotaExceededResponse($e);
        } catch (InvalidAiProviderResponseException $e) {
            return ApiResponse::error(
                code: ApiResponse::AI_PROVIDER_INVALID_RESPONSE,
                message: 'The AI provider returned an invalid response.',
                status: 500,
            );
        } catch (\Exception $e) {
            return ApiResponse::legacyError(
                legacyCode: 'ai_provider_failed',
                message: 'Failed to refine bullet.',
                status: 500,
            );
        }
    }

    /**
     * Generate 3 CV versions in parallel using different angles.
     * Premium gating + quota check handled by 'ai.quota:3' middleware on the route.
     */
    public function generateVersions(
        GenerateResumeVersionsRequest $request,
        Cv $cv,
        GenerateResumeVersionsAction $generateResumeVersions,
    ): JsonResponse {
        Gate::authorize('update', $cv);

        try {
            $user = $request->user();
            $versions = $generateResumeVersions->execute(
                user: $user,
                cv: $cv,
                jobDescription: $request->validated('job_description'),
            );

            return ApiResponse::success(['versions' => $versions, 'quota' => $user->aiQuotaSnapshot()]);
        } catch (InsufficientResumeContentException $e) {
            return ApiResponse::legacyError(
                legacyCode: 'insufficient_resume_content',
                message: $e->getMessage(),
                status: 422,
            );
        } catch (AiQuotaExceededException $e) {
            return $this->quotaExceededResponse($e);
        } catch (InvalidAiProviderResponseException $e) {
            return ApiResponse::error(
                code: ApiResponse::AI_PROVIDER_INVALID_RESPONSE,
                message: 'The AI provider returned an invalid response.',
                status: 500,
            );
        } catch (\Exception $e) {
            return ApiResponse::legacyError(
                legacyCode: 'ai_provider_failed',
                message: 'Failed to generate CV versions.',
                status: 500,
            );
        }
    }

    /**
     * Apply a generated CV version, overwriting the CV's real sections.
     * A snapshot of the prior content is saved before the overwrite.
     */
    public function applyVersion(
        Cv $cv,
        ChameleonAdaptation $adaptation,
        ApplyChameleonAdaptationAction $applyChameleonAdaptation,
    ): JsonResponse {
        Gate::authorize('update', $cv);

        $result = $applyChameleonAdaptation->execute($cv, $adaptation);

        return ApiResponse::success([
            'saved_at' => now()->format('H:i:s'),
            'ats_score' => $result['ats_score'],
            'ats_matched' => $result['ats_matched'],
            'snapshot_id' => $result['snapshot_id'],
        ]);
    }

    private function quotaExceededResponse(AiQuotaExceededException $exception): JsonResponse
    {
        return ApiResponse::legacyError(
            legacyCode: 'quota_exceeded',
            message: $exception->getMessage(),
            status: 402,
            legacy: [
                'remaining' => $exception->remainingCredits,
                'limit' => $exception->quotaLimit,
            ],
            standardCode: ApiResponse::QUOTA_EXCEEDED,
        );
    }
}
