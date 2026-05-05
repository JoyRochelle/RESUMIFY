<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\ProfileController;

// Public Routes
Route::get('/', function () { return view('landing_page.welcome'); })->name('home');
Route::get('/templates', function () { return view('landing_page.templates'); })->name('templates');
Route::get('/pricing', function () { return view('landing_page.pricing'); })->name('pricing');


// Google OAuth Routes
Route::middleware('guest')->group(function ()  {
    Route::get('/auth/google', [SocialAuthController::class, 'redirect'])->name('social.redirect');
    Route::get('/auth/google/callback', [SocialAuthController::class, 'callback'])->name('social.callback');
});

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Customer Routes (verified email required)
    Route::middleware(['role:basic,premium'])->group(function () {
        Route::get('/dashboard', function () {
            return view('user.dashboard');
        })->name('dashboard');

        Route::get('/manuscripts', function () {
            return view('user.manuscript');
        })->name('user.manuscript');

        Route::get('/ai-assistant', function () {
            return view('user.ai-assistant');
        })->name('user.ai-assistant');

        Route::get('/settings', function () {
            return view('user.settings');
        })->name('user.settings');

        Route::get('/help', function () {
            return view('user.help');
        })->name('user.help');

        Route::get('/upgrade-quota', function () {
            return view('user.upgrade-quota');
        })->name('user.upgrade-quota');

        // Profile management routes
        Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
        Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Resumes routes
        Route::resource('resumes',ResumeController::class)->parameters([
            'resumes' => 'cv'
        ]);

        Route::post('resumes/{cv}/duplicate', [ResumeController::class, 'duplicate'])->name('resumes.duplicate');

        Route::put('resumes/{cv}/section/{section}', [ResumeController::class, 'updateSection'])->name('resumes.updateSection');
    });

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard'); 
    });
});
