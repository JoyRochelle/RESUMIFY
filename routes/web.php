<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/manuscript', function () {
    // Tambahkan 'user_dashboard.' di depannya
    return view('user_dashboard.manuscript'); 
})->name('manuscript');


Route::middleware(['auth'])->group(function () {
    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
    });
});