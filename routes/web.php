<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\ResumeController;


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
            return view('dashboard');
        })->name('dashboard');

        // Resume CRUD
        Route::resource('resumes', ResumeController::class)->parameters(['resumes' => 'cv']);

        Route::post('/resumes/{cv}/duplicate', [ResumeController::class, 'duplicate'])
            ->name('resumes.duplicate');
        Route::put('/resumes/{cv}/sections/{section}', [ResumeController::class, 'updateSection'])
            ->name('resumes.sections.update');
    });

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
    });
});



