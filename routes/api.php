<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FixtureController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// auth
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // auth
    Route::post('logout', [AuthController::class, 'logout']);

    // fixture
    Route::get('fixtures', [FixtureController::class, 'index']);
    Route::get('fixtures/{id}', [FixtureController::class, 'show']);

    // booking
    Route::post('bookings', [BookingController::class, 'store']);
    Route::get('bookings', [BookingController::class, 'index']);
    Route::get('bookings/{id}', [BookingController::class, 'show']);
    Route::post('webhook/payment/paid', [BookingController::class, 'payment']);
});
