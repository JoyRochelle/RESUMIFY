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
            return view('user.dashboard');
        })->name('dashboard');
    });

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
    });
});

// User Routes
Route::middleware(['auth', 'role:basic,premium', 'verified'])->group(function () {
    Route::get('/user/manuscripts', function () {
        return view('user.manuscript');
    })->name('user.manuscript');

    Route::get('/user/ai-assistant', function () {
        return view('user.ai-assistant');
    })->name('user.ai-assistant');

    Route::get('/user/settings', function () {
        return view('user.settings');
    })->name('user.settings');

    Route::get('/user/help', function () {
        return view('user.help');
    })->name('user.help');

    Route::get('/user/upgrade-quota', function () {
        return view('user.upgrade-quota');
    })->name('user.upgrade-quota');

    // Resumes routes
    Route::resource('resumes', App\Http\Controllers\ResumeController::class);
    Route::post('resumes/{cv}/duplicate', [App\Http\Controllers\ResumeController::class, 'duplicate'])->name('resumes.duplicate');
    Route::put('resumes/{cv}/section/{section}', [App\Http\Controllers\ResumeController::class, 'updateSection'])->name('resumes.updateSection');
});
