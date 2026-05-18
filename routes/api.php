<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\RoomController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/rooms', [RoomController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);

    Route::middleware('is_admin')->group(function () {
        Route::get('/admin/bookings', [BookingController::class, 'adminIndex']);
        Route::patch('/admin/bookings/{booking}/status', [BookingController::class, 'updateStatus']);
    });
});
