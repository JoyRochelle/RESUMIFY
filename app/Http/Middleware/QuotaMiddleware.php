<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class QuotaMiddleware
{
    /**
     * Check if the authenticated user has enough AI credits remaining.
     *
     * Admins have unlimited quota. Basic and premium users are both
     * metered, each against their own role's limit (config/quota.php).
     *
     * Usage in routes:
     *   ->middleware('ai.quota')       // requires 1 credit
     *   ->middleware('ai.quota:3')     // requires 3 credits
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  int  $creditsRequired  Number of credits this action costs (default: 1)
     */
    public function handle(Request $request, Closure $next, int $creditsRequired = 1): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Admins have unlimited quota — skip check
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Basic and premium users — enforce quota against their own limit
        if ($user->getQuotaRemaining() < $creditsRequired) {
            return response()->json([
                'error'     => 'quota_exceeded',
                'message'   => $user->isPremium()
                    ? 'You have used all your AI credits for this month.'
                    : sprintf('You have used all your AI credits. Upgrade to Premium for %d credits/month.', config('quota.premium')),
                'remaining' => $user->getQuotaRemaining(),
                'limit'     => $user->getQuotaLimit(),
            ], 402);
        }

        return $next($request);
    }
}
