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
     * Premium users and admins bypass the quota check entirely.
     * Free (basic) users must have enough remaining credits.
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

        // Premium users and admins have unlimited (or higher) quota — skip check
        if ($user->isPremium() || $user->isAdmin()) {
            return $next($request);
        }

        // Basic users — enforce quota
        if ($user->getQuotaRemaining() < $creditsRequired) {
            return response()->json([
                'error'     => 'quota_exceeded',
                'message'   => 'You have used all your AI credits. Upgrade to Premium for 50 credits/month.',
                'remaining' => $user->getQuotaRemaining(),
                'limit'     => $user->getQuotaLimit(),
            ], 402);
        }

        return $next($request);
    }
}
