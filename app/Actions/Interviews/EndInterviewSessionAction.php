<?php

namespace App\Actions\Interviews;

use App\Models\AiUsageLog;
use App\Models\InterviewSession;
use App\Models\User;
use App\Services\AiCreditService;
use App\Services\InterviewService;
use Illuminate\Support\Facades\Log;

class EndInterviewSessionAction
{
    public function __construct(
        private InterviewService $interviewService,
        private AiCreditService $aiCreditService,
    ) {}

    public function execute(User $user, InterviewSession $session): EndInterviewSessionResult
    {
        $session->update([
            'status' => 'completed',
            'ended_at' => now(),
        ]);

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
        } catch (\Exception $e) {
            $this->aiCreditService->refund($reservation);
            Log::error('EndInterviewSessionAction feedback failed', ['error' => $e->getMessage()]);

            return EndInterviewSessionResult::feedbackFailed();
        }
    }
}
