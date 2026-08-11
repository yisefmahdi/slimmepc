<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\KlantController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'admin'])
    ->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('dashboard.alias');

        // CMS: website content editor
        Route::prefix('content')
            ->name('content.')
            ->group(function () {
                Route::get('/', [ContentController::class, 'index'])
                    ->name('index');

                Route::post('/design', [ContentController::class, 'updateDesign'])
                    ->name('design');

                Route::post('/{page}/section/{section}', [ContentController::class, 'updateSection'])
                    ->whereAlpha('page')
                    ->whereAlpha('section')
                    ->name('section');
            });

        // Klanten (customers) management
        Route::prefix('klanten')
            ->name('klanten.')
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
    });
