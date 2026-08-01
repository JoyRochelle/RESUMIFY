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

        if (!$user || $user->hasTrialRemaining('interview')) {
            return $next($request);
        }

        return response()->json([
            'error'   => 'trial_used',
            'message' => __('messages.interview.index.trial_exhausted_message', [
                'limit' => $user->getTrialLimit('interview'),
            ]),
        ], 402);
    }
}
