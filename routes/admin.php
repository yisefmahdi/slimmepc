<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ContactInboxController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\KlantController;
use App\Http\Controllers\Admin\RepairInboxController;
use App\Http\Controllers\Admin\AfspraakInboxController;
use App\Http\Controllers\Admin\ManualInvoiceController;
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
                    ->where('page', '[A-Za-z0-9_]+')
                    ->where('section', '[A-Za-z0-9_]+')
                    ->name('section.edit');
                Route::post('/{page}/section/{section}', [ContentController::class, 'updateSection'])
                    ->where('page', '[A-Za-z0-9_]+')
                    ->where('section', '[A-Za-z0-9_]+')
                    ->name('section');

                // Progressive media (image/video) upload used by the section editor.
                Route::post('/media', [ContentController::class, 'uploadMedia'])
                    ->name('media');
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

                Route::get('/reply/{contactReply}/attachment', [ContactInboxController::class, 'replyAttachment'])
                    ->name('reply.attachment');

                Route::delete('/{contactSubmission}', [ContactInboxController::class, 'destroy'])
                    ->name('destroy');
            });

        // Repair submissions (reparatie aanmelden)
        Route::prefix('reparatie-aanmeldingen')
            ->name('reparatie-aanmeldingen.')
            ->group(function () {
                Route::get('/', [RepairInboxController::class, 'index'])
                    ->name('index');

                Route::get('/data', [RepairInboxController::class, 'data'])
                    ->name('data');

                Route::get('/new-count', [RepairInboxController::class, 'newCount'])
                    ->name('new-count');

                Route::get('/{repairSubmission}', [RepairInboxController::class, 'show'])
                    ->name('show');

                Route::post('/{repairSubmission}/status', [RepairInboxController::class, 'status'])
                    ->name('status');

                Route::get('/{repairSubmission}/photo/{file}', [RepairInboxController::class, 'photo'])
                    ->name('photo');

                Route::delete('/{repairSubmission}', [RepairInboxController::class, 'destroy'])
                    ->name('destroy');
            });

        // Afspraak aan huis (afspraak aanvragen)
        Route::prefix('afspraak-aanvragen')
            ->name('afspraak-aanvragen.')
            ->group(function () {
                Route::get('/', [AfspraakInboxController::class, 'index'])->name('index');
                Route::get('/data', [AfspraakInboxController::class, 'data'])->name('data');
                Route::get('/new-count', [AfspraakInboxController::class, 'newCount'])->name('new-count');
                Route::get('/{afspraakSubmission}', [AfspraakInboxController::class, 'show'])->name('show');
                Route::post('/{afspraakSubmission}/status', [AfspraakInboxController::class, 'status'])->name('status');
                Route::delete('/{afspraakSubmission}', [AfspraakInboxController::class, 'destroy'])->name('destroy');
            });

        // Bevestiging-mail - Hardware facturen
        Route::prefix('bevestiging-mail')
            ->name('bevestiging-mail.')
            ->group(function () {
                Route::prefix('hardware')
                    ->name('hardware.')
                    ->group(function () {
                        Route::get('/', [ManualInvoiceController::class, 'index'])->name('index');
                        Route::get('/data', [ManualInvoiceController::class, 'data'])->name('data');
                        Route::get('/create', [ManualInvoiceController::class, 'create'])->name('create');
                        Route::post('/', [ManualInvoiceController::class, 'store'])->name('store');
                        Route::get('/{invoice}/download', [ManualInvoiceController::class, 'download'])->name('download');
                        Route::delete('/{invoice}', [ManualInvoiceController::class, 'destroy'])->name('destroy');
                    });
            });
    });

