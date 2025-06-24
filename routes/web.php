<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

Route::get('/book', [BookingController::class, 'bookingForm'])->name('booking.form');
Route::get('/status', [BookingController::class, 'statusPage'])->name('booking.status');

Route::get('/payment/{booking_code}', [PaymentController::class, 'showPaymentPage'])->name('payment.process');
Route::post('/payment/process', [PaymentController::class, 'processPayment'])->name('payment.submit');
Route::get('/payment/success/{booking_code}', [PaymentController::class, 'showSuccessPage'])->name('payment.success');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
