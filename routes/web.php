<?php

use App\Http\Controllers\Account\OrderController;
use App\Http\Controllers\AfspraakController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\LidmaatschapController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\VideoStreamController;
use App\Http\Controllers\WebshopController;
use App\Http\Controllers\WebshopReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/stream/video/{file}', [VideoStreamController::class, 'show'])
    ->where('file', '.*')
    ->name('video.stream');

Route::get('/', [PageController::class, 'home'])->name('home');

Route::get('/tarieven', [PageController::class, 'tarieven'])->name('tarieven');

Route::get('/contact', [PageController::class, 'contact'])->name('contact');

Route::get('/over-ons', [PageController::class, 'overons'])->name('over-ons');

Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');

Route::get('/voorwaarden', [PageController::class, 'voorwaarden'])->name('voorwaarden');

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

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
Route::post('/cart/items', [CartController::class, 'store'])->middleware('throttle:30,1')->name('cart.items.store');
Route::patch('/cart/items/{item}', [CartController::class, 'update'])->middleware('throttle:30,1')->name('cart.items.update');
Route::delete('/cart/items/{item}', [CartController::class, 'destroy'])->middleware('throttle:30,1')->name('cart.items.destroy');
Route::delete('/cart', [CartController::class, 'clear'])->middleware('throttle:10,1')->name('cart.clear');
Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->middleware('throttle:20,1')->name('cart.coupon.apply');
Route::delete('/cart/coupon', [CartController::class, 'removeCoupon'])->middleware('throttle:20,1')->name('cart.coupon.remove');

// Product search (?q=) — site-wide across all categories, same webshop design
Route::get('/zoeken', [WebshopController::class, 'search'])->name('zoeken');
// Webshop — product details (must be before category route)
Route::get('/webshop/{categorySlug}/{productSlug}', [WebshopController::class, 'show'])->name('webshop.product');
Route::post('/webshop/{categorySlug}/{productSlug}/reviews', [WebshopReviewController::class, 'store'])->middleware('throttle:5,1')->name('webshop.reviews.store');
// Webshop — category page (only /webshop/{slug}, no general /webshop)
Route::get('/webshop/{slug}', [WebshopController::class, 'index'])->name('webshop.category');

// Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/totals', [CheckoutController::class, 'totals'])->middleware('throttle:30,1')->name('checkout.totals');
Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:10,1')->name('checkout.store');

// Monteur (technician) — login, klantfactuur aan huis, Mollie
Route::get('/betaal/login', [TechnicianController::class, 'technicialogin'])->name('technician.login');
Route::post('/betaal/login', [TechnicianController::class, 'loginSubmit'])->middleware('throttle:10,1')->name('technician.login.submit');
Route::get('/technician/payment/{klantnummer}', [TechnicianController::class, 'paymentPage'])->name('technician.payment');
Route::post('/technician/payment/submit', [TechnicianController::class, 'storePaymentForm'])->middleware('throttle:10,1')->name('technician.payment.store');
Route::post('/technician/quote', [TechnicianController::class, 'quote'])->middleware('throttle:30,1')->name('technician.quote');
Route::post('/technician/check-coupon', [TechnicianController::class, 'checkCoupon'])->middleware('throttle:30,1')->name('technician.coupon.check');
Route::post('/technician/webhook', [TechnicianController::class, 'webhook'])->name('technician.webhook');
Route::get('/technician/return/{form}', [TechnicianController::class, 'mollieReturn'])->name('technician.return');
Route::get('/technician/success/{form}', [TechnicianController::class, 'success'])->name('technician.success');
Route::get('/technician/failed/{form}', [TechnicianController::class, 'failed'])->name('technician.failed');

// Lidmaatschap (lid-worden) — ook zonder login mogelijk
Route::get('/lid-worden', [LidmaatschapController::class, 'show'])->name('lidmaatschap.show');
Route::post('/lid-worden', [LidmaatschapController::class, 'store'])->middleware('throttle:5,1')->name('lidmaatschap.store');
Route::post('/lid-worden/webhook', [LidmaatschapController::class, 'webhook'])->name('lidmaatschap.webhook');
Route::get('/lid-worden/success/{lidmaatschap}', [LidmaatschapController::class, 'success'])->name('lidmaatschap.success');
Route::get('/lid-worden/failed/{lidmaatschap}', [LidmaatschapController::class, 'failed'])->name('lidmaatschap.failed');
Route::get('/lid-worden/return/{lidmaatschap}', [LidmaatschapController::class, 'mollieReturn'])->name('lidmaatschap.return');

// Mollie payments (single reusable gateway)
Route::post('/payment/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');
Route::get('/payment/return/{order}', [PaymentController::class, 'return'])->name('payment.return');
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/failed', [PaymentController::class, 'failed'])->name('payment.failed');

// Live Chat (AI + medewerker) — CSRF-protected, throttle per endpoint
Route::prefix('ai-chat')->name('ai-chat.')->group(function () {
    Route::get('/status', [App\Http\Controllers\ChatController::class, 'status'])->name('status');
    Route::post('/start', [App\Http\Controllers\ChatController::class, 'start'])->middleware('throttle:10,1')->name('start');
    Route::get('/messages', [App\Http\Controllers\ChatController::class, 'messages'])->middleware('throttle:60,1')->name('messages');
    Route::post('/send', [App\Http\Controllers\ChatController::class, 'send'])->middleware('throttle:30,1')->name('send');
    Route::post('/handover', [App\Http\Controllers\ChatController::class, 'handover'])->middleware('throttle:10,1')->name('handover');
    Route::post('/offline', [App\Http\Controllers\ChatController::class, 'offline'])->middleware('throttle:5,1')->name('offline');
    Route::post('/close', [App\Http\Controllers\ChatController::class, 'close'])->middleware('throttle:10,1')->name('close');
    Route::post('/rate', [App\Http\Controllers\ChatController::class, 'rate'])->middleware('throttle:10,1')->name('rate');
    Route::get('/history', [App\Http\Controllers\ChatController::class, 'history'])->middleware('throttle:30,1')->name('history');
    Route::get('/photo/{message}', [App\Http\Controllers\ChatController::class, 'photo'])->middleware('throttle:60,1')->name('photo');
});

// Wishlist — login required (guests are sent to login, then back via intended URL)
Route::middleware('auth')->group(function () {
    Route::get('/wishlist', [FavoriteController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [FavoriteController::class, 'toggle'])->middleware('throttle:30,1')->name('wishlist.toggle');
    Route::delete('/wishlist/{favorite}', [FavoriteController::class, 'destroy'])->middleware('throttle:30,1')->name('wishlist.destroy');
});

// Customer order history ("Mijn bestellingen") — login required, strictly user-scoped
Route::middleware('auth')->group(function () {
    Route::get('/mijn-bestellingen', [OrderController::class, 'index'])->name('account.orders.index');
    Route::get('/mijn-bestellingen/{orderNumber}', [OrderController::class, 'show'])->name('account.orders.show');
    Route::get('/mijn-bestellingen/{orderNumber}/factuur', [OrderController::class, 'invoice'])->name('account.orders.invoice');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Public Device Receipt Tracking
Route::get('/track', [TrackingController::class, 'index'])->name('tracking.index');
Route::post('/track', [TrackingController::class, 'track'])->name('tracking.track');

require __DIR__.'/auth.php';
