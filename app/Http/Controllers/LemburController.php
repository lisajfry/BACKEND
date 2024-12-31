<?php

namespace App\Http\Controllers;

use App\Models\Lembur;
use Illuminate\Http\Request;

class LemburController extends Controller
{
    public function index(Request $request)
{
    // Get logged-in user ID
    $userId = auth()->user()->id;

    // Get bulan (month) and tahun (year) from the request
    $bulan = $request->input('bulan');
    $tahun = $request->input('tahun');

    // Start the query for lembur, filtering by the logged-in user's ID
    $lemburQuery = Lembur::where('id_karyawan', $userId);

    // If bulan and tahun are provided, filter by those as well
    if ($bulan && $tahun) {
        $lemburQuery->whereYear('tanggal_lembur', $tahun)
                    ->whereMonth('tanggal_lembur', $bulan);
    }

    // Get the lembur records
    $lembur = $lemburQuery->get();

    return response()->json($lembur);
}


 // Menambahkan data lembur baru
 public function store(Request $request)
 {
     // Get logged-in user ID
     $userId = auth()->user()->id;
 
     // Validasi input
     $validated = $request->validate([
         'tanggal_lembur' => 'required|date',
         'jam_mulai' => 'required|date_format:H:i',
         'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
         'alasan_lembur' => 'nullable|string',
     ]);
 
     // Tambahkan ID Karyawan secara otomatis
     $validated['id_karyawan'] = $userId;
 
     // Cek apakah sudah ada lembur pada tanggal yang sama untuk karyawan ini
     $existingLembur = Lembur::where('id_karyawan', $userId)
         ->where('tanggal_lembur', $validated['tanggal_lembur'])
         ->first();
 
     if ($existingLembur) {
         return response()->json([
             'message' => 'Lembur pada tanggal ini sudah ada untuk karyawan yang sama.',
         ], 422); // HTTP 422 Unprocessable Entity
     }
 
     // Hitung durasi lembur secara otomatis (dalam jam)
     $start = \Carbon\Carbon::createFromFormat('H:i', $validated['jam_mulai']);
     $end = \Carbon\Carbon::createFromFormat('H:i', $validated['jam_selesai']);
     $validated['durasi_lembur'] = $start->diffInMinutes($end) / 60; // Hitung dalam jam
 
     // Simpan data lembur
     $lembur = Lembur::create($validated);
 
     return response()->json([
         'message' => 'Data lembur berhasil ditambahkan',
         'data' => $lembur,
     ], 201);
 }
 

}