<?php

namespace App\Actions\Admin;

use App\Models\AdminLog;
use App\Models\User;

class AdjustUserCreditsAction
{
    public function execute(User $admin, User $user, int $quotaUsed): void
    {
        $oldQuotaUsed = (int) $user->ai_quota_used;

        $user->forceFill(['ai_quota_used' => $quotaUsed])->save();

        AdminLog::create([
            'admin_id' => $admin->id,
            'action' => 'adjust_credits',
            'target_type' => 'user',
            'target_id' => $user->id,
            'metadata' => [
                'field' => 'ai_quota_used',
                'old' => $oldQuotaUsed,
                'new' => $quotaUsed,
            ],
        ]);
    }
}
