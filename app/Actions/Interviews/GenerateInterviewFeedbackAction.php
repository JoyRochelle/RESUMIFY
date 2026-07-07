<?php

namespace App\Actions\Interviews;

use App\Exceptions\InvalidAiProviderResponseException;
use App\Models\AiUsageLog;
use App\Models\InterviewSession;
use App\Models\User;
use App\Services\AiCreditService;
use App\Services\InterviewService;
use Illuminate\Support\Facades\Log;

/**
 * Generate (or regenerate) the STAR feedback report for a completed
 * session. Extracted from EndInterviewSessionAction so sessions that ended
 * without a report — e.g. while the provider was rejecting the feedback
 * schema — can recover it from the conversation page.
 */
class GenerateInterviewFeedbackAction
{
    public function __construct(
        private InterviewService $interviewService,
        private AiCreditService $aiCreditService,
    ) {}

    public function execute(User $user, InterviewSession $session): EndInterviewSessionResult
    {
        $reservation = $this->aiCreditService->reserve($user, 1, 'interview_feedback');

        if ($reservation->isDenied()) {
            return EndInterviewSessionResult::feedbackUnavailable();
        }

        try {
            $this->interviewService->generateFeedback($session);

            AiUsageLog::create([
                'user_id' => $user->id,
                'action_type' => 'interview_feedback',
                'resume_id' => $session->resume_id,
                'tokens_used' => 0,
                'cost_usd' => 0,
            ]);

            return EndInterviewSessionResult::feedbackGenerated();
        } catch (InvalidAiProviderResponseException $e) {
            $this->aiCreditService->refund($reservation);
            Log::warning('GenerateInterviewFeedbackAction invalid AI feedback response', ['error' => $e->getMessage()]);

            return EndInterviewSessionResult::invalidProviderResponse();
        } catch (\Exception $e) {
            $this->aiCreditService->refund($reservation);
            Log::error('GenerateInterviewFeedbackAction feedback failed', ['error' => $e->getMessage()]);

            return EndInterviewSessionResult::feedbackFailed();
        }
    }
}
