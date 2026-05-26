<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ResetAiQuota extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'quota:reset';

    /**
     * The console command description.
     */
    protected $description = 'Reset AI quota for users whose monthly billing cycle has passed';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = User::where(function ($query) {
            $query->where('ai_quota_reset_at', '<=', now()->subMonth())
                  ->orWhereNull('ai_quota_reset_at');
        })
        ->where('ai_quota_used', '>', 0)
        ->update([
            'ai_quota_used'     => 0,
            'ai_quota_reset_at' => now(),
        ]);

        $this->info("Reset AI quota for {$count} user(s).");

        return self::SUCCESS;
    }
}
