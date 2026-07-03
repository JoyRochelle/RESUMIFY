<?php

namespace App\Policies;

use App\Models\AtsScan;
use App\Models\User;

class AtsScanPolicy
{
    public function view(User $user, AtsScan $scan): bool
    {
        return $user->id === $scan->user_id;
    }

    public function delete(User $user, AtsScan $scan): bool
    {
        return $user->id === $scan->user_id;
    }
}
