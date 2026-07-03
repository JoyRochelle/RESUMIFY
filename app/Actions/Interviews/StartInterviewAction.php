<?php

namespace App\Actions\Interviews;

use App\Models\AiUsageLog;
use App\Models\Cv;
use App\Models\User;
use App\Services\AiCreditService;
use App\Services\InterviewService;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StartInterviewAction
{
    public function __construct(
        private InterviewService $interviewService,
        private AiCreditService $aiCreditService,
    ) {}

    /**
     * @return array{session: \App\Models\InterviewSession, message: string}
     */
    public function execute(User $user, Cv $resume, string $jobTarget): array
    {
        $reservation = $this->aiCreditService->reserve($user, 1, 'interview_start');

        if ($reservation->isDenied()) {
            throw new HttpException(402, 'AI quota exceeded.');
        }

        try {
            $result = $this->interviewService->startSession($user, $resume, $jobTarget);

            AiUsageLog::create([
                'user_id' => $user->id,
                'action_type' => 'interview_question',
                'resume_id' => $resume->id,
                'tokens_used' => 0,
                'cost_usd' => 0,
            ]);

            return $result;
        } catch (\Exception $e) {
            $this->aiCreditService->refund($reservation);
            Log::error('StartInterviewAction failed', ['error' => $e->getMessage()]);

            throw $e;
        }
    }
}
