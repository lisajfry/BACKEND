<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AbsensiController;

// Route for user registration
Route::post('register', [AuthController::class, 'register']);

// Route for user login
Route::post('login', [AuthController::class, 'login']);

// Group routes that require API authentication
Route::middleware('auth:api')->group(function () {
    // Route for user logout
    Route::post('logout', [AuthController::class, 'logout']);

    // Route to get logged-in user details
    Route::get('karyawan', [AuthController::class, 'getKaryawan']);

    // Profile routes
    Route::get('profile', [App\Http\Controllers\Api\ProfileController::class, 'index']);
    Route::put('profile/update', [App\Http\Controllers\Api\ProfileController::class, 'update']);
    Route::post('profile/upload-avatar', [App\Http\Controllers\Api\ProfileController::class, 'uploadAvatar']);

    // Absensi routes
    Route::get('absensi', [AbsensiController::class, 'index']);
    Route::post('absensi/masuk', [AbsensiController::class, 'absenMasuk']);
    Route::post('absensi/keluar', [AbsensiController::class, 'absenKeluar']);
});
