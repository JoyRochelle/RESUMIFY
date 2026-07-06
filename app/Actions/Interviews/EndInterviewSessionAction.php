<?php

namespace App\Actions\Interviews;

use App\Models\InterviewSession;
use App\Models\User;

class EndInterviewSessionAction
{
    public function __construct(
        private GenerateInterviewFeedbackAction $generateFeedback,
    ) {}

    public function execute(User $user, InterviewSession $session): EndInterviewSessionResult
    {
        $session->update([
            'status' => 'completed',
            'ended_at' => now(),
        ]);

        return $this->generateFeedback->execute($user, $session);
    }
}
