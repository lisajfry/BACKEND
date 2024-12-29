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
     $validated = $request->validate([
         'id_karyawan' => 'required|exists:karyawans,id',
         'tanggal_lembur' => 'required|date',
         'jam_mulai' => 'required|date_format:H:i:s',
         'jam_selesai' => 'required|date_format:H:i:s|after:jam_mulai',
         'alasan_lembur' => 'nullable|string',
     ]);

     // Hitung durasi lembur secara otomatis (dalam jam)
     $start = strtotime($validated['jam_mulai']);
     $end = strtotime($validated['jam_selesai']);
     $validated['durasi_lembur'] = round(($end - $start) / 3600, 2); // Hitung selisih dalam jam dengan 2 desimal

     $lembur = Lembur::create($validated);

     return response()->json(['message' => 'Data lembur berhasil ditambahkan', 'data' => $lembur], 201);
 }

}