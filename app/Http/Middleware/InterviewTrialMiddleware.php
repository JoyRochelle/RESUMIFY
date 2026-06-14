<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InterviewTrialMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || $user->isPremium() || $user->isAdmin()) {
            return $next($request);
        }

        if ($user->interviewSessions()->exists()) {
            return response()->json([
                'error'   => 'trial_used',
                'message' => 'Your free trial has been used. Upgrade to Premium for unlimited sessions.',
            ], 402);
        }

        return $next($request);
    }
}
