<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\LemburController;
use App\Http\Controllers\IzinController;
use App\Http\Controllers\DinasLuarKotaController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\LokasiKantorController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\Api\ProfileController;


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
    Route::get('profile', [ProfileController::class, 'index']);
    Route::put('profile/update', [ProfileController::class, 'update']);
    Route::post('profile/upload-avatar', [ProfileController::class, 'uploadAvatar']);

    // Absensi routes
    Route::get('absensi', [AbsensiController::class, 'index']);
    Route::post('absensi/masuk', [AbsensiController::class, 'absenMasuk']);
    Route::post('absensi/keluar', [AbsensiController::class, 'absenKeluar']);
    Route::get('/foto_masuk/{filename}', [AbsensiController::class, 'getFoto']);




     // Rute lembur
     Route::get('lembur', [LemburController::class, 'index']); // Mengambil semua data lembur
     Route::post('lembur', [LemburController::class, 'store']); // Membuat entri lembur baru
     Route::get('lembur/{id}', [LemburController::class, 'show']); // Mengambil entri lembur tertentu berdasarkan ID
     Route::put('lembur/{id}', [LemburController::class, 'update']); // Memperbarui entri lembur tertentu berdasarkan ID
     Route::delete('lembur/{id}', [LemburController::class, 'destroy']); // Menghapus entri lembur tertentu berdasarkan ID
 



    Route::apiResource('jabatan', JabatanController::class);





 
    Route::post('/izin', [IzinController::class, 'store']);     // Menambah izin
    Route::get('/izin', [IzinController::class, 'index']);      // Mengambil semua izin
    Route::put('/izin/{id}', [IzinController::class, 'update']); // Mengupdate izin berdasarkan ID
    Route::get('/izin/{id}', [IzinController::class, 'show']);   // Mengambil izin berdasarkan ID
    Route::delete('/izin/{id}', [IzinController::class, 'destroy']); // Menghapus izin berdasarkan ID




Route::get('/dinas-luar-kota', [DinasLuarKotaController::class, 'index']);
Route::post('/dinas-luar-kota', [DinasLuarKotaController::class, 'store']);
Route::get('/dinas-luar-kota/{id}', [DinasLuarKotaController::class, 'show']);
Route::put('/dinas-luar-kota/{id}', [DinasLuarKotaController::class, 'update']);
Route::delete('/dinas-luar-kota/{id}', [DinasLuarKotaController::class, 'destroy']);

Route::get('/payroll-summary', [PayrollController::class, 'getPayrollSummary']);
});



