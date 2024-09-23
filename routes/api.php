<?php

// Mengimpor AuthController untuk menangani request terkait autentikasi
use App\Http\Controllers\AuthController;
// Mengimpor AbsensiController untuk menangani request absensi
use App\Http\Controllers\AbsensiController;

// Route untuk registrasi pengguna baru
Route::post('register', [AuthController::class, 'register']);
// Route untuk login pengguna
Route::post('login', [AuthController::class, 'login']);

// Kelompok route yang membutuhkan autentikasi API
Route::middleware('auth:api')->group(function() {
    // Route untuk logout pengguna, hanya bisa diakses jika pengguna terautentikasi
    Route::post('logout', [AuthController::class, 'logout']);
    // Route untuk mendapatkan informasi pengguna yang sedang login, hanya bisa diakses jika pengguna terautentikasi
    Route::get('user', [AuthController::class, 'getUser']);

    Route::post('absen/masuk', [AbsensiController::class, 'absenMasuk']);
    
    Route::post('absen/keluar', action: [AbsensiController::class, 'absenKeluar']);
    
    // Route untuk melihat riwayat absensi pengguna
    Route::get('/riwayat-absensi', [AbsensiController::class, 'riwayatAbsensi']);
});
