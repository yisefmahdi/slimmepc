<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\AfspraakController;
use App\Http\Controllers\VideoStreamController;
use Illuminate\Support\Facades\Route;

Route::get('/stream/video/{file}', [VideoStreamController::class, 'show'])
    ->where('file', '.*')
    ->name('video.stream');

Route::get('/', [PageController::class, 'home'])->name('home');

Route::get('/tarieven', [PageController::class, 'tarieven'])->name('tarieven');

Route::get('/contact', [PageController::class, 'contact'])->name('contact');

Route::get('/over-ons', [PageController::class, 'overons'])->name('over-ons');

Route::get('/reparatie-aanmelden', [PageController::class, 'reparatie'])->name('reparatie');

Route::get('/diensten/{slug}', [PageController::class, 'service'])->name('service.show');

Route::post('/contact/submit', [ContactController::class, 'submit'])
    ->middleware('throttle:5,1')
    ->name('contact.submit');

Route::post('/reparatie/submit', [RepairController::class, 'submit'])
    ->middleware('throttle:5,1')
    ->name('reparatie.submit');

Route::get('/afspraak', [PageController::class, 'afspraak'])->name('afspraak');
Route::post('/afspraak/submit', [AfspraakController::class, 'submit'])
    ->middleware('throttle:5,1')
    ->name('afspraak.submit');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Public Device Receipt Tracking
Route::get('/track', [App\Http\Controllers\TrackingController::class, 'index'])->name('tracking.index');
Route::post('/track', [App\Http\Controllers\TrackingController::class, 'track'])->name('tracking.track');

require __DIR__.'/auth.php';

