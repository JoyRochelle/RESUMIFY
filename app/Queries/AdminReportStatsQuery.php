<?php

namespace App\Queries;

use App\Models\AiUsageLog;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

class AdminReportStatsQuery
{
    /**
     * Get aggregate statistics for the admin dashboard within a date range.
     */
    public function get(Carbon $from, Carbon $to): array
    {
        return [
            'new_users'           => User::whereBetween('created_at', [$from, $to])->count(),
            'premium_conversions' => Transaction::where('status', 'success')
                                        ->whereBetween('paid_at', [$from, $to])
                                        ->count(),
            'total_ai_calls'      => AiUsageLog::whereBetween('created_at', [$from, $to])->count(),
            'total_ai_cost'       => (float) AiUsageLog::whereBetween('created_at', [$from, $to])->sum('cost_usd'),
            'total_revenue'       => (float) Transaction::where('status', 'success')
                                        ->whereBetween('paid_at', [$from, $to])
                                        ->sum('amount'),
        ];
    }
}
