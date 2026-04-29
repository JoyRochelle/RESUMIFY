<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialAuthController;


Route::get('/', function () {
    return view('welcome');
});


// Google OAuth Routes
Route::middleware('guest')->group(function ()  {
    Route::get('/auth/google', [SocialAuthController::class, 'redirect'])->name('social.redirect');
    Route::get('/auth/google/callback', [SocialAuthController::class, 'callback'])->name('social.callback');
});

// Authenticated Routes
Route::middleware(['auth'])->group(function () {

    // Customer Routes (verified email required)
    Route::middleware(['role:basic,premium', 'verified'])->group(function () {
        Route::get('/dashboard', function () {
            return view('user_dashboard.dashboard');
        })->name('dashboard');

        Route::get('/manuscript', function () {
            return view('user_dashboard.manuscript');
        })->name('manuscript');

        Route::get('/ai-assistant', function () {
            return view('user_dashboard.dashboard'); // Placeholder
        })->name('ai-assistant');

        Route::get('/settings', function () {
            return view('user_dashboard.dashboard'); // Placeholder
        })->name('settings');

        Route::get('/help', function () {
            return view('user_dashboard.dashboard'); // Placeholder
        })->name('help');
    });

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
    });
});

// Public Routes (Placeholders)
Route::get('/pricing', function () {
    return view('welcome'); // Redirect to welcome/pricing section if needed
})->name('pricing');

Route::get('/templates', function () {
    return view('welcome');
})->name('templates');
