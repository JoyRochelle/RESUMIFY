<?php

namespace App\Actions\Interviews;

use App\Models\AiUsageLog;
use App\Models\InterviewSession;
use App\Models\User;
use App\Services\InterviewService;
use Illuminate\Support\Facades\Log;

class SendInterviewMessageAction
{
    public function __construct(private InterviewService $interviewService) {}

    public function execute(User $user, InterviewSession $session, string $content): string
    {
        $user->increment('ai_quota_used', 1);

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
            $user->decrement('ai_quota_used', 1);
            Log::error('SendInterviewMessageAction failed', ['error' => $e->getMessage()]);

            throw $e;
        }
    }
}
