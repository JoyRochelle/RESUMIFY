<?php

namespace App\Actions\Admin;

use App\Models\AdminLog;
use App\Models\User;

class ToggleUserSuspensionAction
{
    public function execute(User $admin, User $user): bool
    {
        $wasSuspended = (bool) $user->is_suspended;
        $isSuspended = ! $wasSuspended;

        $user->forceFill(['is_suspended' => $isSuspended])->save();

        AdminLog::create([
            'admin_id' => $admin->id,
            'action' => $wasSuspended ? 'activate_user' : 'suspend_user',
            'target_type' => 'user',
            'target_id' => $user->id,
            'metadata' => [
                'field' => 'is_suspended',
                'old' => $wasSuspended,
                'new' => $isSuspended,
            ],
        ]);

        return $isSuspended;
    }
}
