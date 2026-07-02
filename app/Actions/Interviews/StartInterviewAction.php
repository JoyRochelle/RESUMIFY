<?php

namespace App\Actions\Interviews;

use App\Models\AiUsageLog;
use App\Models\Cv;
use App\Models\User;
use App\Services\InterviewService;
use Illuminate\Support\Facades\Log;

class StartInterviewAction
{
    public function __construct(private InterviewService $interviewService) {}

    /**
     * @return array{session: \App\Models\InterviewSession, message: string}
     */
    public function execute(User $user, Cv $resume, string $jobTarget): array
    {
        $user->increment('ai_quota_used', 1);

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
            $user->decrement('ai_quota_used', 1);
            Log::error('StartInterviewAction failed', ['error' => $e->getMessage()]);

            throw $e;
        }
    }
}
