
<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\BookmarkController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
// Public Authentication Routes
Route::post('/login', [UserController::class, 'login']);
Route::post('/signup', [UserController::class, 'signup']);

// Protected Logout Route
Route::middleware('auth:sanctum')->post('/logout', [UserController::class, 'logout']);

// User Routes (Admin only for certain actions)
Route::middleware('auth:sanctum', 'can:admin')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});

// User Routes (Accessible by anyone)
Route::middleware('auth:sanctum')->group(function () {
    Route::put('/users/{id}', [UserController::class, 'update']);
});

// CarRoutes (Accessible by anyone)
Route::get('/vehicles', [CarController::class, 'index']);

// Vehicle Routes (Owner only)
Route::middleware('auth:sanctum', 'can:owner')->group(function () {
    Route::post('/vehicles', [VehicleController::class, 'store']);
    Route::put('/vehicles/{id}', [VehicleController::class, 'update']);
    Route::delete('/vehicles/{id}', [VehicleController::class, 'destroy']);
});

// Booking Routes (Accessible by anyone)
Route::get('/bookings', [BookingController::class, 'index']);

// Booking Routes (Renter and Admin)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/bookings', [BookingController::class, 'store']);// Renter
    Route::put('/bookings/{id}', [BookingController::class, 'update']);// Owner
    Route::delete('/bookings/{id}', [BookingController::class, 'destroy']);// Renter
});

// Review Routes (Accessible by anyone)
Route::get('/reviews', [ReviewController::class, 'index']); // No middleware for anyone

// Review Routes (Renter only)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
});

// Bookmark Routes (Renter only)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/bookmarks', [BookmarkController::class, 'index']);
    Route::post('/bookmarks', [BookmarkController::class, 'store']);
    Route::delete('/bookmarks/{id}', [BookmarkController::class, 'destroy']);
});

