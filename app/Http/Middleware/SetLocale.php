<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = config('app.supported_locales', ['en']);
        $user = $request->user();

        $locale = null;

        if ($user && in_array($user->locale, $supported, true)) {
            $locale = $user->locale;
        } elseif (in_array($request->session()->get('locale'), $supported, true)) {
            $locale = $request->session()->get('locale');
        }

        app()->setLocale($locale ?? config('app.fallback_locale', 'en'));

        return $next($request);
    }
}
