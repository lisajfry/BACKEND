<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    // Metode index tetap sama
    public function index()
    {
        $absensi = Absensi::all();
        return response()->json($absensi);
    }

    // Metode untuk absen masuk
    public function absenMasuk(Request $request)
    {
        $validatedData = $request->validate([
            'id_karyawan' => 'required|exists:karyawans,id',
            'tanggal' => 'required|date',
            'jam_masuk' => 'required|date_format:H:i:s',
            'foto_masuk' => 'nullable|string', // Tetap gunakan string
            'latitude_masuk' => 'nullable|numeric',
            'longitude_masuk' => 'nullable|numeric',
            'lokasi_masuk' => 'nullable|string',
            'status' => 'required|in:hadir,alfa,izin,sakit', // Validasi status
        ]);

        $absensi = Absensi::create($validatedData);
        return response()->json($absensi, 201);
    }

    public function absenKeluar(Request $request)
    {
        $validatedData = $request->validate([
            'id_karyawan' => 'required|exists:karyawans,id',
            'tanggal' => 'required|date',
            'jam_keluar' => 'required|date_format:H:i:s',
            'foto_keluar' => 'nullable|string', // Tetap gunakan string
            'latitude_keluar' => 'nullable|numeric',
            'longitude_keluar' => 'nullable|numeric',
            'lokasi_keluar' => 'nullable|string',
            'status' => 'required|in:hadir,alfa,izin,sakit', // Validasi status
        ]);

        $absensi = Absensi::where('id_karyawan', $validatedData['id_karyawan'])
            ->where('tanggal', $validatedData['tanggal'])
            ->firstOrFail();

        $absensi->update($validatedData);
        return response()->json($absensi);
    }
}
