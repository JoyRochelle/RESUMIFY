<?php

namespace App\Policies;

use App\Models\InterviewSession;
use App\Models\User;

class InterviewSessionPolicy
{
    public function view(User $user, InterviewSession $session): bool
    {
        return $this->ownsSession($user, $session);
    }

    public function endSession(User $user, InterviewSession $session): bool
    {
        return $this->ownsSession($user, $session);
    }

    public function feedback(User $user, InterviewSession $session): bool
    {
        return $this->ownsSession($user, $session);
    }

    public function stream(User $user, InterviewSession $session): bool
    {
        return $this->ownsSession($user, $session);
    }

    public function message(User $user, InterviewSession $session): bool
    {
        return $this->ownsSession($user, $session);
    }

    private function ownsSession(User $user, InterviewSession $session): bool
    {
        return $user->id === $session->user_id;
    }
}
