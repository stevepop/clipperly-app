<?php

use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/time-slots', [BookingController::class, 'getTimeSlots']);
    Route::post('/bookings', [BookingController::class, 'storeBooking']);
    Route::post('/check-status', [BookingController::class, 'checkStatus']);
});
