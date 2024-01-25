<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected routes that require authentication via Sanctum
Route::middleware(['auth:sanctum'])->group(function () {
    // Logout route
    Route::post('/logout', [AuthController::class, 'logout']);

    // User Profile route
    Route::get('/profile', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('/bookings', BookingController::class);
});
