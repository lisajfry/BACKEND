<?php

namespace App\Http\Controllers;

use App\Models\DinasLuarKota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Tambahkan ini

class DinasLuarKotaController extends Controller
{
    public function index(Request $request)
{
    $userId = Auth::id();
    if (!$userId) {
        return response()->json(['message' => 'User not authenticated'], 401);
    }

    // Ambil parameter bulan dan tahun dari query string
    $bulan = $request->query('bulan');
    $tahun = $request->query('tahun');

    // Query dinas luar kota dengan filter bulan dan tahun jika ada
    $dinasLuarKota = DinasLuarKota::where('id_karyawan', $userId)
        ->when($bulan, function ($query) use ($bulan) {
            return $query->whereMonth('tgl_berangkat', $bulan);
        })
        ->when($tahun, function ($query) use ($tahun) {
            return $query->whereYear('tgl_berangkat', $tahun);
        })
        ->get();

    return response()->json($dinasLuarKota);
}



    
    

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'tgl_berangkat' => 'required|date',
            'tgl_kembali' => 'required|date|after_or_equal:tgl_berangkat',
            'kota_tujuan' => 'required|string',
            'keperluan' => 'required|string',
            'biaya_transport' => 'required|numeric',
            'biaya_penginapan' => 'required|numeric',
            'uang_harian' => 'required|numeric',
        ]);

        // Ambil id_karyawan dari pengguna yang sedang login
        $id_karyawan = Auth::user()->id;

        // Hitung total biaya
        $totalBiaya = $request->biaya_transport + $request->biaya_penginapan + ($request->uang_harian * $this->calculateDays($request->tgl_berangkat, $request->tgl_kembali));

        // Simpan data dinas luar kota ke dalam database
        $dinasLuarKota = DinasLuarKota::create([
            'id_karyawan' => $id_karyawan, // Gunakan id_karyawan dari pengguna yang sedang login
            'tgl_berangkat' => $request->tgl_berangkat,
            'tgl_kembali' => $request->tgl_kembali,
            'kota_tujuan' => $request->kota_tujuan,
            'keperluan' => $request->keperluan,
            'biaya_transport' => $request->biaya_transport,
            'biaya_penginapan' => $request->biaya_penginapan,
            'uang_harian' => $request->uang_harian,
            'total_biaya' => $totalBiaya,
        ]);

        return response()->json(['message' => 'Dinas luar kota berhasil ditambahkan', 'data' => $dinasLuarKota], 201);
    }

    // Fungsi untuk menghitung jumlah hari antara dua tanggal
    private function calculateDays($startDate, $endDate)
    {
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        return $end->diffInDays($start);
    }

    public function show($id)
    {
        $dinasLuarKota = DinasLuarKota::findOrFail($id);
        return response()->json($dinasLuarKota);
    }

    public function update(Request $request, $id)
    {
        \Log::info('Request data:', $request->all()); // Log data request
    
        // Validasi input
        $request->validate([
            'tgl_berangkat' => 'nullable|date',
            'tgl_kembali' => 'nullable|date|after_or_equal:tgl_berangkat',
            'kota_tujuan' => 'nullable|string',
            'keperluan' => 'nullable|string',
            'biaya_transport' => 'nullable|numeric',
            'biaya_penginapan' => 'nullable|numeric',
            'uang_harian' => 'nullable|numeric',
        ]);
    
        $dinasLuarKota = DinasLuarKota::findOrFail($id);
        
        // Update data dinas luar kota hanya jika ada perubahan
        if ($request->has('tgl_berangkat')) {
            $dinasLuarKota->tgl_berangkat = $request->tgl_berangkat;
        }
        
        if ($request->has('tgl_kembali')) {
            $dinasLuarKota->tgl_kembali = $request->tgl_kembali;
        }
    
        if ($request->has('kota_tujuan')) {
            $dinasLuarKota->kota_tujuan = $request->kota_tujuan;
        }
    
        if ($request->has('keperluan')) {
            $dinasLuarKota->keperluan = $request->keperluan;
        }
    
        if ($request->has('biaya_transport')) {
            $dinasLuarKota->biaya_transport = $request->biaya_transport;
        }
    
        if ($request->has('biaya_penginapan')) {
            $dinasLuarKota->biaya_penginapan = $request->biaya_penginapan;
        }
    
        if ($request->has('uang_harian')) {
            $dinasLuarKota->uang_harian = $request->uang_harian;
        }
    
        // Hitung ulang total biaya
        $totalBiaya = $dinasLuarKota->biaya_transport + $dinasLuarKota->biaya_penginapan + ($dinasLuarKota->uang_harian * $this->calculateDays($dinasLuarKota->tgl_berangkat, $dinasLuarKota->tgl_kembali));
        $dinasLuarKota->total_biaya = $totalBiaya; // Update total biaya
    
        // Log data sebelum disimpan
        \Log::info('Data before save:', $dinasLuarKota->toArray());
        
        $dinasLuarKota->save();
    
        return response()->json(['message' => 'Dinas luar kota berhasil diupdate', 'data' => $dinasLuarKota]);
    }
    


    public function destroy($id)
    {
        $dinasLuarKota = DinasLuarKota::findOrFail($id);
        $dinasLuarKota->delete();

        return response()->json(['message' => 'Dinas luar kota berhasil dihapus']);
    }
}
