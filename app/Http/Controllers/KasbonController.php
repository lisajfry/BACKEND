<?php

namespace App\Http\Controllers;

use App\Models\Kasbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KasbonController extends Controller
{
    public function index()
    {
        // Ambil hanya data kasbon milik karyawan yang sedang login
        $user = Auth::user();
        return Kasbon::with(['pembayaran'])
            ->where('id_karyawan', $user->id)
            ->get();
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'tanggal_pengajuan' => 'required|date',
            'jumlah_kasbon' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        // Simpan kasbon untuk user yang sedang login
        $kasbon = Kasbon::create([
            'id_karyawan' => Auth::id(),
            'tanggal_pengajuan' => $request->tanggal_pengajuan,
            'jumlah_kasbon' => $request->jumlah_kasbon,
            'keterangan' => $request->keterangan,
        ]);

        return $kasbon;
    }


    
    // Endpoint untuk laporan kasbon
    public function laporan()
    {
        // Ambil ID karyawan yang sedang login
        $karyawanId = Auth::id();

        // Ambil semua kasbon milik karyawan yang sedang login beserta pembayaran terkait
        $kasbons = Kasbon::where('id_karyawan', $karyawanId)->with('pembayaran')->get();

        // Menghitung total kasbon, total pembayaran, dan sisa kasbon
        $totalKasbon = 0;
        $totalDibayar = 0;

        // Looping untuk menghitung total kasbon dan total yang sudah dibayar
        foreach ($kasbons as $kasbon) {
            $totalKasbon += $kasbon->jumlah_kasbon; // Total kasbon
            $totalDibayar += $kasbon->pembayaran->sum('jumlah_dibayar'); // Total pembayaran yang sudah dilakukan
        }

        // Menghitung sisa yang harus dibayar
        $sisaKasbon = $totalKasbon - $totalDibayar;

        // Mengembalikan hasil laporan
        return response()->json([
            'total_kasbon' => $totalKasbon,
            'total_dibayar' => $totalDibayar,
            'sisa_kasbon' => $sisaKasbon
        ]);
    }


    public function show($id)
    {
        $kasbon = Kasbon::with(['pembayaran'])
            ->where('id', $id)
            ->where('id_karyawan', Auth::id()) // Hanya data milik karyawan login
            ->firstOrFail();

        return $kasbon;
    }
}
