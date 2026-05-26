<?php

namespace App\Policies;

use App\Models\Cv;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CvPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Cv $cv): bool
    {
        return $user->id === $cv->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Cv $cv): bool
    {
        return $user->id === $cv->user_id;
    }

    public function delete(User $user, Cv $cv): bool
    {
        return $user->id === $cv->user_id;
    }

    public function restore(User $user, Cv $cv): bool
    {
        return $user->id === $cv->user_id;
    }

    public function forceDelete(User $user, Cv $cv): bool
    {
        return $user->id === $cv->user_id;
    }
}
