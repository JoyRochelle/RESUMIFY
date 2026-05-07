<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\ResumeExportController;

// Public Routes
Route::get('/', function () { return view('landing_page.welcome'); })->name('home');
Route::get('/templates', function () {
    $templates = \App\Models\CvTemplate::where('is_active', true)->orderBy('sort_order')->get();
    return view('landing_page.templates', compact('templates'));
})->name('templates');
Route::get('/pricing', function () { return view('landing_page.pricing'); })->name('pricing');


// OAuth Routes
Route::middleware('guest')->group(function ()  {
    Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])->name('social.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');
});

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Customer Routes (verified email required)
    Route::middleware(['role:basic,premium'])->group(function () {
        Route::get('/dashboard', function () {
            $templates = \App\Models\CvTemplate::where('is_active', true)->orderBy('sort_order')->get();
            return view('user.dashboard', compact('templates'));
        })->name('dashboard');

        Route::get('/manuscripts', function () {
            $cv = auth()->user()->cvs()->latest()->first();
            
            // If they have no CVs at all, force them to the dashboard to pick a template
            if (!$cv) {
                return redirect()->route('dashboard', ['create' => 'true']);
            }
            
            $templates = \App\Models\CvTemplate::where('is_active', true)->orderBy('sort_order')->get();
            return view('user.manuscript', compact('templates', 'cv'));
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

        // Public Template Preview
        Route::get('/templates/{template}/preview', [\App\Http\Controllers\Admin\TemplateController::class, 'preview'])->name('templates.preview');

        // Resumes routes
        Route::resource('resumes',ResumeController::class)->parameters([
            'resumes' => 'cv'
        ]);

        Route::post('resumes/{cv}/duplicate', [ResumeController::class, 'duplicate'])->name('resumes.duplicate');

        Route::put('resumes/{cv}/section/{section}', [ResumeController::class, 'updateSection'])->name('resumes.updateSection');

        Route::get('resumes/{cv}/preview', [ResumeExportController::class, 'preview'])->name('resumes.preview');
        Route::get('resumes/{cv}/pdf', [ResumeExportController::class, 'downloadPdf'])->name('resumes.pdf');
    });

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard'); 
        Route::get('/users', function () {
            return view('admin.users');
        })->name('users');
        
        Route::get('/support', function () {
            return view('admin.support');
        })->name('support');

        // Template Library CRUD
        Route::resource('templates', TemplateController::class);
        Route::patch('templates/{template}/toggle', [TemplateController::class, 'toggle'])->name('templates.toggle');
        Route::get('templates/{template}/preview', [TemplateController::class, 'preview'])->name('templates.preview');
        
        Route::get('/logs', function () {
            return view('admin.logs');
        })->name('logs');
        
        Route::get('/settings', function () {
            return view('admin.settings');
        })->name('settings');
    });
});
