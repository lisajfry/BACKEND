<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\LemburController;
use App\Http\Controllers\IzinController;
use App\Http\Controllers\DinasLuarKotaController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\KasbonController;
use App\Http\Controllers\PembayaranController;



// Route for user registration
Route::post('register', [AuthController::class, 'register']);

// Route for user login
Route::post('login', [AuthController::class, 'login']);

Route::get('office-location', [AbsensiController::class, 'getOfficeLocation']);


// Group routes that require API authentication
Route::middleware('auth:api')->group(function () {
    // Route for user logout
    Route::post('logout', [AuthController::class, 'logout']);

    // Route untuk memverifikasi token
    
    Route:
Route::get('verify-token', function (Request $request) {
        return response()->json(['message' => 'Token valid'], 200);
    });


    // Route to get logged-in user details
    Route::get('karyawan', [AuthController::class, 'getKaryawan']);

    // Profile routes
    Route::get('profile', [ProfileController::class, 'index']);
    Route::put('profile', [ProfileController::class, 'update']);
    Route::post('profile/avatar', [ProfileController::class, 'uploadAvatar']);

    // Absensi routes
    Route::get('absensi', [AbsensiController::class, 'index']);
    Route::post('absensi/masuk', [AbsensiController::class, 'absenMasuk']);
    Route::post('absensi/keluar', [AbsensiController::class, 'absenKeluar']);
    Route::get('/foto_masuk/{filename}', [AbsensiController::class, 'getFoto']);
    // routes/api.php




Route::apiResource('kasbon', KasbonController::class);
Route::post('kasbon', [KasbonController::class, 'store']); // Membuat entri lembur baru

Route::post('pembayaran', [PembayaranController::class, 'store']);
Route::get('laporan', [KasbonController::class, 'laporan']);


Route::get('/generate-slip-gaji', [PayrollController::class, 'generateSlipGaji']);



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

Route::get('/tasks', [TaskController::class, 'index']); // Menampilkan semua tugas berdasarkan karyawan
    Route::get('/tasks/{id_tugas}', [TaskController::class, 'show']); // Menampilkan detail tugas
    Route::post('/tasks', [TaskController::class, 'store']); // Membuat tugas baru
    Route::put('/tasks/{id_tugas}', [TaskController::class, 'update']); // Mengupdate tugas
    Route::delete('/tasks/{id_tugas}', [TaskController::class, 'destroy']); // Menghapus tugas

Route::get('/payroll-summary', [PayrollController::class, 'getPayrollSummary']);
});



