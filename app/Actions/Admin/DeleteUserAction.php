<?php

namespace App\Actions\Admin;

use App\Models\AdminLog;
use App\Models\User;

class DeleteUserAction
{
    public function execute(User $admin, User $user): void
    {
        AdminLog::create([
            'admin_id' => $admin->id,
            'action' => 'hard_delete_user',
            'target_type' => 'user',
            'target_id' => $user->id,
            'metadata' => [
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);

        $user->forceDelete();
    }
}
