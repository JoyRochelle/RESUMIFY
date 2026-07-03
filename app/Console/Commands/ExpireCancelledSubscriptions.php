<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;

class ExpireCancelledSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire-cancelled';

    protected $description = 'Expire cancelled subscriptions after their billing period ends';

    public function handle(): int
    {
        $count = 0;

        Subscription::query()
            ->with('user')
            ->where('plan', 'premium')
            ->where('status', 'cancelled')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now())
            ->each(function (Subscription $subscription) use (&$count) {
                $subscription->update(['status' => 'expired']);

                $user = $subscription->user;

                $hasActivePremium = $user->subscriptions()
                    ->where('plan', 'premium')
                    ->whereIn('status', ['active', 'cancelled'])
                    ->where(function ($query) {
                        $query->whereNull('ends_at')
                            ->orWhere('ends_at', '>', now());
                    })
                    ->exists();

                if (!$hasActivePremium && $user->role === 'premium') {
                    $user->forceFill(['role' => 'basic'])->save();
                }

                $count++;
            });

        $this->info("Expired {$count} cancelled subscription(s).");

        return self::SUCCESS;
    }
}
