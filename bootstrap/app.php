<?php

use App\Http\Middleware\CheckSuspended;
use App\Http\Middleware\InterviewTrialMiddleware;
use App\Http\Middleware\QuotaMiddleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SecurityHeadersMiddleware;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->append(SecurityHeadersMiddleware::class);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'ai.quota' => QuotaMiddleware::class,
            'suspended' => CheckSuspended::class,
            'interview.trial' => InterviewTrialMiddleware::class,
        ]);

        $middleware->appendToGroup('web', CheckSuspended::class);
        $middleware->appendToGroup('web', SetLocale::class);

        $middleware->validateCsrfTokens(except: [
            'payment/callback',
        ]);

        $middleware->redirectUsersTo(function () {
            if (auth()->check() && auth()->user()->isAdmin()) {
                return '/admin/dashboard';
            }

            return '/dashboard';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
