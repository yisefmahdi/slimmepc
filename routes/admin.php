<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ContactInboxController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\KlantController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'admin', 'inbound.sync'])
    ->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('dashboard.alias');

        // CMS: website content editor
        Route::prefix('content')
            ->name('content.')
            ->group(function () {
                // Redirect /admin/content to design editor
                Route::get('/', function () {
                    return redirect()->route('admin.content.design.edit');
                })->name('index');

                // Design / SEO Settings
                Route::get('/design', [ContentController::class, 'editDesign'])
                    ->name('design.edit');
                Route::post('/design', [ContentController::class, 'updateDesign'])
                    ->name('design');

                // Section editors (Header, Hero, Why)
                Route::get('/{page}/section/{section}', [ContentController::class, 'editSection'])
                    ->whereAlpha('page')
                    ->whereAlpha('section')
                    ->name('section.edit');
                Route::post('/{page}/section/{section}', [ContentController::class, 'updateSection'])
                    ->whereAlpha('page')
                    ->whereAlpha('section')
                    ->name('section');
            });

        // Users management (formerly klanten)
        Route::prefix('users')
            ->name('users.')
            ->group(function () {
                Route::get('/', [KlantController::class, 'index'])
                    ->name('index');

                Route::get('/data', [KlantController::class, 'data'])
                    ->name('data');

                Route::post('/', [KlantController::class, 'store'])
                    ->name('store');

                Route::get('/{klant}', [KlantController::class, 'show'])
                    ->name('show');

                Route::put('/{klant}', [KlantController::class, 'update'])
                    ->name('update');

                Route::delete('/{klant}', [KlantController::class, 'destroy'])
                    ->name('destroy');

                Route::post('/{klant}/toggle-block', [KlantController::class, 'toggleBlock'])
                    ->name('toggle-block');

                Route::post('/{klant}/role', [KlantController::class, 'updateRole'])
                    ->name('role');
            });

        // Contact inbox (submissions + chat threads)
        Route::prefix('contact-inbox')
            ->name('contact-inbox.')
            ->group(function () {
                Route::get('/', [ContactInboxController::class, 'index'])
                    ->name('index');

                Route::get('/data', [ContactInboxController::class, 'data'])
                    ->name('data');

                Route::get('/new-count', [ContactInboxController::class, 'newCount'])
                    ->name('new-count');

                // Pulls inbound e-mail replies immediately (throttled ~15s).
                // Called by the inbox page every 30 seconds while it is open.
                Route::post('/sync', [ContactInboxController::class, 'sync'])
                    ->name('sync');

                Route::get('/{contactSubmission}', [ContactInboxController::class, 'show'])
                    ->name('show');

                Route::post('/{contactSubmission}/reply', [ContactInboxController::class, 'reply'])
                    ->name('reply');

                Route::post('/{contactSubmission}/status', [ContactInboxController::class, 'status'])
                    ->name('status');

                Route::get('/{contactSubmission}/attachment', [ContactInboxController::class, 'attachment'])
                    ->name('attachment');

                Route::delete('/{contactSubmission}', [ContactInboxController::class, 'destroy'])
                    ->name('destroy');
            });
    });

