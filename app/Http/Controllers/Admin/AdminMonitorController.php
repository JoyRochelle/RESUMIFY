<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class AdminMonitorController extends Controller
{
    public function index(): View
    {
        // --- Queue health ---
        $pendingJobs = DB::table('jobs')->count();
        $failedJobs  = DB::table('failed_jobs')->count();

        // jobs.created_at is a unix timestamp integer
        $lastJobTs  = DB::table('jobs')->orderByDesc('created_at')->value('created_at');
        $lastJobAt  = $lastJobTs ? \Carbon\Carbon::createFromTimestamp($lastJobTs) : null;

        // --- Disk usage ---
        [$diskFree, $diskTotal] = $this->diskUsage();
        $diskUsed    = $diskTotal - $diskFree;
        $diskPct     = $diskTotal > 0 ? round(($diskUsed / $diskTotal) * 100, 1) : 0;

        // --- Sentry errors (cached 5 min, nullable on failure) ---
        $sentryErrors = $this->fetchSentryErrors();

        // --- Recent failed jobs (last 10) ---
        $recentFailed = DB::table('failed_jobs')
            ->orderByDesc('failed_at')
            ->limit(10)
            ->get(['id', 'queue', 'failed_at', 'exception']);

        return view('admin.monitor', compact(
            'pendingJobs', 'failedJobs', 'lastJobAt',
            'diskFree', 'diskTotal', 'diskUsed', 'diskPct',
            'sentryErrors', 'recentFailed'
        ));
    }

    private function diskUsage(): array
    {
        try {
            $path  = storage_path('app/public');
            $free  = disk_free_space($path);
            $total = disk_total_space($path);

            return [$free ?: 0, $total ?: 0];
        } catch (\Throwable) {
            return [0, 0];
        }
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
                // Sentry is best-effort — never block the render
            }

            return null;
        });
    }
}
