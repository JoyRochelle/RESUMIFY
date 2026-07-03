<?php

namespace App\Http\Controllers;

use App\Actions\Ai\GenerateResumeVersionsAction;
use App\Actions\Ai\RefineResumeBulletAction;
use App\Exceptions\AiQuotaExceededException;
use App\Exceptions\InsufficientResumeContentException;
use App\Exceptions\InvalidAiProviderResponseException;
use App\Http\Requests\GenerateResumeVersionsRequest;
use App\Http\Requests\RefineResumeBulletRequest;
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
            $options = $refineResumeBullet->execute(
                user: $request->user(),
                cv: $cv,
                text: $request->validated('text'),
                jobContext: $request->validated('job_context'),
            );

            return ApiResponse::success(['options' => $options]);
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
            $versions = $generateResumeVersions->execute(
                user: $request->user(),
                cv: $cv,
                jobDescription: $request->validated('job_description'),
            );

            return ApiResponse::success(['versions' => $versions]);
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
