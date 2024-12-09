<?php

/// app/Http/Controllers/PembayaranController.php
namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Kasbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends Controller
{
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'kasbon_id' => 'required|exists:kasbons,id', // Validasi ID Kasbon
            'tanggal_pembayaran' => 'required|date',
            'jumlah_dibayar' => 'required|numeric|min:0',
        ]);

        // Ambil ID karyawan yang sedang login
        $karyawanId = Auth::id();

        // Cari kasbon yang memiliki ID karyawan yang sama dengan yang login
        $kasbon = Kasbon::where('id', $request->kasbon_id)
            ->where('id_karyawan', $karyawanId)
            ->firstOrFail(); // Jika tidak ditemukan, maka akan gagal (404)

        // Simpan pembayaran
        $pembayaran = Pembayaran::create([
            'kasbon_id' => $kasbon->id,
            'tanggal_pembayaran' => $request->tanggal_pembayaran,
            'jumlah_dibayar' => $request->jumlah_dibayar,
        ]);

        // Update status kasbon menjadi "Lunas" jika pembayaran sudah cukup
        if ($kasbon->pembayaran->sum('jumlah_dibayar') >= $kasbon->jumlah_kasbon) {
            $kasbon->update(['status' => 'Lunas']);
        }

        return response()->json($pembayaran, 201); // Kembalikan data pembayaran yang baru
    }
}
