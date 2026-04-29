<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// // Authenticated Routes
// Route::middleware(['auth'])->group(function () {

    // Customer Routes (verified email required)
    // Route::middleware(['role:basic,premium', 'verified'])->group(function () {
        
        Route::get('/dashboard', function () {
            return view('user_dashboard.dashboard');
        })->name('dashboard');

        Route::get('/assistant', function () {
            return view('user_dashboard.ai-assistant'); 
        })->name('assistant');

        Route::get('/manuscript', function () {
            return view('user_dashboard.manuscript');
        })->name('manuscript');

        // Route untuk Halaman Settings
        Route::get('/settings', function () {
            return view('user_dashboard.settings');
        })->name('settings');

        Route::get('/help', function (){
            return view('user_dashboard.help');
        })->name('help');

        Route::get('/upgrade', function (){
            return view('user_dashboard.upgrade-quota');
        })->name('upgrade');
    // });

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
    });
// });