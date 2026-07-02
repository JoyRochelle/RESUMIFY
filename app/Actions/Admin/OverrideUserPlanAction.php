<?php

namespace App\Actions\Admin;

use App\Models\AdminLog;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OverrideUserPlanAction
{
    public function execute(User $admin, User $user, string $plan): void
    {
        DB::transaction(function () use ($admin, $user, $plan): void {
            $oldRole = $user->role;

            $user->forceFill(['role' => $plan])->save();

            if ($plan === 'premium') {
                Subscription::create([
                    'user_id' => $user->id,
                    'plan' => 'premium',
                    'status' => 'active',
                    'starts_at' => now(),
                    'ends_at' => now()->addMonth(),
                ]);

                $user->forceFill(['ai_quota_used' => 0])->save();
            }

            AdminLog::create([
                'admin_id' => $admin->id,
                'action' => 'override_plan',
                'target_type' => 'user',
                'target_id' => $user->id,
                'metadata' => [
                    'field' => 'role',
                    'old' => $oldRole,
                    'new' => $plan,
                ],
            ]);
        });
    }
}
