<?php

use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () { return view('landing_page.welcome'); })->name('home');
Route::get('/templates', function () { return view('landing_page.templates'); })->name('templates');
Route::get('/pricing', function () { return view('landing_page.pricing'); })->name('pricing');

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Customer Routes (Basic & Premium)
    Route::middleware(['role:basic,premium'])->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
    });

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');
    });
});