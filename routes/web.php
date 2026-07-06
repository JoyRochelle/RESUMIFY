<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminLogController;
use App\Http\Controllers\Admin\AdminMonitorController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\SupportTicketController;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Billing\PaymentController;
use App\Http\Controllers\Billing\SubscriptionController;
use App\Http\Controllers\Public\LandingPageController;
use App\Http\Controllers\Support\HelpController;
use App\Http\Controllers\User\Ai\AiResumeController;
use App\Http\Controllers\User\Ai\AtsController;
use App\Http\Controllers\User\Interview\InterviewController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\Resume\ManuscriptAtsController;
use App\Http\Controllers\User\Resume\ResumeController;
use App\Http\Controllers\User\Resume\ResumeExportController;
use App\Http\Controllers\User\Resume\ResumeSnapshotController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::controller(LandingPageController::class)->group(function () {
    Route::get('/', 'welcome')->name('home');
    Route::get('/templates', 'templates')->name('templates');
    Route::get('/pricing', 'pricing')->name('pricing');
});

// Public Template Demo Preview (dummy data only — no user data exposed)
Route::get('/templates/{template}/demo', [TemplateController::class, 'preview'])
    ->name('templates.demo');

// Webhook Route
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

// OAuth Routes
Route::middleware('guest')->group(function () {
    Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])->name('social.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');
});

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('notifications')->controller(NotificationController::class)->name('notifications.')->group(function () {
        Route::post('/read-all', 'readAll')->name('readAll');
        Route::get('/{id}/read', 'read')->name('read');
    });

    // Customer Routes (verified email required)
    Route::middleware(['role:basic,premium'])->group(function () {

        // Dashboard & Main Pages
        Route::controller(UserController::class)->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard');
            Route::get('/manuscripts', 'manuscript')->name('user.manuscript');
            Route::get('/settings', 'settings')->name('user.settings');
            Route::get('/upgrade-quota', 'upgradeQuota')->name('user.upgrade-quota');
        });

        // Help Center Routes
        Route::prefix('help')->controller(HelpController::class)->group(function () {
            Route::get('/', 'index')->name('user.help');
            Route::post('/contact', 'contact')->name('help.contact');
            Route::get('/tickets', 'tickets')->name('help.tickets');
            Route::get('/tickets/{ticket}', 'showTicket')->name('help.tickets.show');
            Route::post('/tickets/{ticket}/reply', 'reply')->name('help.tickets.reply');
        });

        // Payment Routes
        Route::post('/payment/create', [PaymentController::class, 'create'])->name('payment.create');
        Route::post('/subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');

        // Profile Management Routes
        Route::prefix('profile')->controller(ProfileController::class)->name('profile.')->group(function () {
            Route::post('/avatar', 'updateAvatar')->name('avatar.update');
            Route::delete('/avatar', 'deleteAvatar')->name('avatar.delete');
            Route::delete('/', 'destroy')->name('destroy');
        });

        // Resumes Resource
        Route::resource('resumes', ResumeController::class)->parameters([
            'resumes' => 'cv',
        ]);

        // Resumes Nested Routes
        Route::prefix('resumes/{cv}')->name('resumes.')->group(function () {
            // General actions
            Route::controller(ResumeController::class)->group(function () {
                Route::post('/duplicate', 'duplicate')->name('duplicate');
                Route::patch('/template', 'updateTemplate')->name('updateTemplate');

                // Section actions
                Route::prefix('section')->group(function () {
                    Route::post('/', 'storeSection')->name('sections.store');
                    Route::put('/{section}', 'updateSection')->name('updateSection');
                    Route::delete('/{section}', 'destroySection')->name('sections.destroy');
                });
            });

            // ATS actions
            Route::post('/ats-score', [ManuscriptAtsController::class, 'score'])
                ->middleware('throttle:5,1')
                ->name('atsScore');

            // Export & Preview actions
            Route::controller(ResumeExportController::class)->group(function () {
                Route::get('/preview', 'preview')->name('preview');
                Route::get('/pdf', 'downloadPdf')->name('pdf');
            });

            // Section history (snapshots) actions
            Route::prefix('history')->controller(ResumeSnapshotController::class)->name('history.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/{snapshot}/restore', 'restore')->name('restore');
            });

            // AI Features actions
            Route::prefix('ai')->controller(AiResumeController::class)->name('ai.')->group(function () {
                Route::post('/refine-bullet', 'refineBullet')
                    ->middleware(['ai.quota:1', 'throttle:10,1'])
                    ->name('refineBullet');

                Route::post('/generate-versions', 'generateVersions')
                    ->middleware(['ai.quota:3', 'throttle:3,1'])
                    ->name('generateVersions');

                Route::post('/versions/{adaptation}/apply', 'applyVersion')
                    ->name('applyVersion');
            });
        });

        // Mock Interview Routes
        Route::prefix('interview')->controller(InterviewController::class)->name('interview.')->group(function () {
            // Page routes
            Route::get('/', 'index')->name('index');
            Route::get('/history', 'history')->name('history');
            Route::get('/sessions/{session}', 'show')->name('show');
            Route::get('/sessions/{session}/feedback', 'feedback')->name('feedback');
            Route::post('/sessions/{session}/feedback/generate', 'generateFeedback')->name('feedback.generate');
            Route::post('/sessions/{session}/end', 'endSession')->name('end');

            // API routes
            Route::post('/start', 'start')
                ->middleware(['ai.quota:1', 'interview.trial'])
                ->name('start');
            Route::post('/sessions/{session}/message', 'message')
                ->middleware('ai.quota:1')
                ->name('message');
            Route::post('/sessions/{session}/stream', 'stream')
                ->middleware('ai.quota:1')
                ->name('stream');
        });

        // AI Global Features — ATS Analyzer
        Route::prefix('ats')->controller(AtsController::class)->name('ats.')->group(function () {
            Route::get('/', 'index')->name('index');                    // GET  /ats  (also aliased as user.ai-assistant)
            Route::post('/analyze', 'analyze')                          // POST /ats/analyze
                ->middleware(['ai.quota:1', 'throttle:5,1'])
                ->name('analyze');
            Route::get('/history/{scan}', 'showHistory')                // GET  /ats/history/{scan}
                ->name('history.show');
            Route::delete('/history/{scan}', 'destroyHistory')          // DELETE /ats/history/{scan}
                ->name('history.destroy');
        });

        // Alias: keep the old named route so existing nav links don't break
        Route::get('/ai-assistant', [AtsController::class, 'index'])->name('user.ai-assistant');
    });

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // User Management Routes
        Route::prefix('users')->controller(AdminUserController::class)->name('users')->group(function () {
            Route::get('/', 'index'); // Maps to admin.users
            Route::get('/{user}', 'show')->name('.show');
            Route::patch('/{user}/plan', 'overridePlan')->name('.plan');
            Route::patch('/{user}/credits', 'adjustCredits')->name('.credits');
            Route::patch('/{user}/suspend', 'toggleSuspend')->name('.suspend');
            Route::delete('/{user}', 'destroy')->name('.destroy');
        });

        // Support Ticket Routes
        Route::prefix('support')->controller(SupportTicketController::class)->name('support')->group(function () {
            Route::get('/', 'index'); // Maps to admin.support
            Route::get('/{ticket}', 'show')->name('.show');
            Route::post('/{ticket}/reply', 'reply')->name('.reply');
            Route::patch('/{ticket}/assign', 'assign')->name('.assign');
            Route::patch('/{ticket}/status', 'updateStatus')->name('.status');
            Route::patch('/{ticket}/request-close', 'requestClose')->name('.request-close');
            Route::patch('/{ticket}/confirm-close', 'confirmClose')->name('.confirm-close');
            Route::patch('/{ticket}/reject-close', 'rejectClose')->name('.reject-close');
        });

        // Template Library CRUD
        Route::resource('templates', TemplateController::class);
        Route::prefix('templates/{template}')->controller(TemplateController::class)->name('templates.')->group(function () {
            Route::patch('/toggle', 'toggle')->name('toggle');
            Route::get('/preview', 'preview')->name('preview');
        });

        // Log Routes
        Route::prefix('logs')->controller(AdminLogController::class)->name('logs')->group(function () {
            Route::get('/', 'index'); // Maps to admin.logs
            Route::get('/export/ai', 'exportAiCsv')->name('.export.ai');
            Route::get('/export/finance', 'exportFinanceCsv')->name('.export.finance');
        });

        Route::get('/monitor', [AdminMonitorController::class, 'index'])->name('monitor');

        // Report Routes
        Route::prefix('reports')->controller(AdminReportController::class)->name('reports')->group(function () {
            Route::get('/', 'index'); // Maps to admin.reports
            Route::get('/export/pdf', 'exportPdf')->name('.export.pdf');
            Route::get('/export/csv', 'exportCsv')->name('.export.csv');
        });

        Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('settings');
    });
});
