<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiUsageLog;
use App\Models\SupportTicket;
use App\Models\Transaction;
use App\Models\User;
use App\Models\CvTemplate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $now = now();

        // --- Stats cards ---
        $totalRevenue = Transaction::where('status', 'success')
            ->whereMonth('paid_at', $now->month)
            ->whereYear('paid_at', $now->year)
            ->sum('amount');

        $totalRevenuePrev = Transaction::where('status', 'success')
            ->whereMonth('paid_at', $now->copy()->subMonth()->month)
            ->whereYear('paid_at', $now->copy()->subMonth()->year)
            ->sum('amount');

        $revenueGrowth = $totalRevenuePrev > 0
            ? round((($totalRevenue - $totalRevenuePrev) / $totalRevenuePrev) * 100, 1)
            : null;

        $aiCostUsd = AiUsageLog::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->sum('cost_usd');

        $openTickets    = SupportTicket::where('status', 'open')->count();
        $premiumUsers   = User::where('role', 'premium')->count();

        // --- Chart data: last 30 days ---
        $chartData = $this->buildChartData();

        // --- Sentry health (cached 5 min, never blocks render) ---
        $sentryErrors = $this->fetchSentryErrors();

        // --- Recent user activity: 5 most recently registered users ---
        $recentUsers = User::whereIn('role', ['basic', 'premium'])
            ->latest()
            ->limit(5)
            ->get(['id', 'name', 'email', 'role', 'created_at']);

        // --- Support queue: 3 most recent open tickets ---
        $recentTickets = SupportTicket::where('status', 'open')
            ->with('user:id,name')
            ->latest()
            ->limit(3)
            ->get();

        // --- Template performance: top 4 templates by CV usage ---
        $templatePerformance = CvTemplate::withCount('cvs')
            ->where('is_active', true)
            ->orderByDesc('cvs_count')
            ->limit(4)
            ->get(['id', 'name']);

        $maxCvCount = $templatePerformance->max('cvs_count') ?: 1;

        return view('admin.dashboard', compact(
            'totalRevenue',
            'revenueGrowth',
            'aiCostUsd',
            'openTickets',
            'premiumUsers',
            'chartData',
            'sentryErrors',
            'recentUsers',
            'recentTickets',
            'templatePerformance',
            'maxCvCount',
        ));
    }

    private function buildChartData(): array
    {
        $days   = collect(range(29, 0))->map(fn($i) => now()->subDays($i)->toDateString());
        $from   = now()->subDays(29)->startOfDay();
        $to     = now()->endOfDay();

        $newUsers = DB::table('users')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('date')
            ->pluck('count', 'date');

        $aiCalls = DB::table('ai_usage_logs')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('date')
            ->pluck('count', 'date');

        return [
            'labels'      => $days->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'))->values()->all(),
            'activeUsers' => $days->map(fn($d) => (int) ($newUsers[$d] ?? 0))->values()->all(),
            'aiCalls'     => $days->map(fn($d) => (int) ($aiCalls[$d] ?? 0))->values()->all(),
        ];
    }

    private function fetchSentryErrors(): ?int
    {
        $token   = config('services.sentry.auth_token');
        $org     = config('services.sentry.org_slug');
        $project = config('services.sentry.project_slug');

        if (!$token || !$org || !$project) {
            return null;
        }

        return Cache::remember('sentry_error_count', 300, function () use ($token, $org, $project) {
            try {
                $response = Http::timeout(5)
                    ->withToken($token)
                    ->get("https://sentry.io/api/0/projects/{$org}/{$project}/issues/", [
                        'query'       => 'is:unresolved',
                        'statsPeriod' => '24h',
                        'limit'       => 1,
                    ]);

                if ($response->successful()) {
                    return (int) $response->header('X-Hits') ?: count($response->json());
                }
            } catch (\Throwable) {
                // Sentry API is best-effort — never block the dashboard render
            }

            return null;
        });
    }
}
