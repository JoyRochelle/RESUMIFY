<?php

namespace App\Actions\Interviews;

use App\Models\AiUsageLog;
use App\Models\InterviewSession;
use App\Models\User;
use App\Services\AiCreditService;
use App\Services\InterviewService;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SendInterviewMessageAction
{
    public function __construct(
        private InterviewService $interviewService,
        private AiCreditService $aiCreditService,
    ) {}

    public function execute(User $user, InterviewSession $session, string $content): string
    {
        $reservation = $this->aiCreditService->reserve($user, 1, 'interview_message');

        if ($reservation->isDenied()) {
            throw new HttpException(402, 'AI quota exceeded.');
        }

        try {
            $reply = $this->interviewService->sendMessage($session, $content);

            AiUsageLog::create([
                'user_id' => $user->id,
                'action_type' => 'interview_question',
                'resume_id' => $session->resume_id,
                'tokens_used' => 0,
                'cost_usd' => 0,
            ]);

            return $reply;
        } catch (\Exception $e) {
            $this->aiCreditService->refund($reservation);
            Log::error('SendInterviewMessageAction failed', ['error' => $e->getMessage()]);

            throw $e;
        }
    }
}
