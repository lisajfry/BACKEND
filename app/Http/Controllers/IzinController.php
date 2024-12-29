<?php

namespace App\Http\Controllers;

use App\Models\Izin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IzinController extends Controller
{
    // Mengambil semua izin untuk user yang sedang login
    public function index(Request $request)
{
    $userId = Auth::id();
    if (!$userId) {
        return response()->json(['message' => 'User not authenticated'], 401);
    }

    // Ambil parameter bulan dan tahun dari query string
    $bulan = $request->query('bulan');
    $tahun = $request->query('tahun');

    // Query izin dengan filter bulan dan tahun jika ada
    $izins = Izin::where('id_karyawan', $userId)
        ->when($bulan, function ($query) use ($bulan) {
            $query->whereMonth('tgl_mulai', $bulan);
        })
        ->when($tahun, function ($query) use ($tahun) {
            $query->whereYear('tgl_mulai', $tahun);
        })
        ->get();

    return response()->json($izins);
}



    // Menambah izin baru
    public function store(Request $request)
{
    $request->validate([
        'tgl_mulai' => 'required|date',
        'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
        'alasan' => 'required|string',
        'keterangan' => 'nullable|string',
    ]);

    $idKaryawan = Auth::id();

    // Cek apakah sudah ada izin di rentang tanggal yang sama
    $existingIzin = Izin::where('id_karyawan', $idKaryawan)
        ->where(function ($query) use ($request) {
            $query->whereBetween('tgl_mulai', [$request->tgl_mulai, $request->tgl_selesai])
                  ->orWhereBetween('tgl_selesai', [$request->tgl_mulai, $request->tgl_selesai])
                  ->orWhere(function ($query) use ($request) {
                      $query->where('tgl_mulai', '<=', $request->tgl_mulai)
                            ->where('tgl_selesai', '>=', $request->tgl_selesai);
                  });
        })
        ->exists();

    if ($existingIzin) {
        return response()->json([
            'message' => 'Pengajuan izin pada tanggal ini sudah ada. Anda hanya dapat mengajukan izin satu kali untuk rentang tanggal yang sama.'
        ], 400);
    }

    $durasi = now()->parse($request->tgl_selesai)->diffInDays($request->tgl_mulai) + 1;

    $izin = Izin::create([
        'id_karyawan' => $idKaryawan,
        'tgl_mulai' => $request->tgl_mulai,
        'tgl_selesai' => $request->tgl_selesai,
        'alasan' => $request->alasan,
        'keterangan' => $request->keterangan,
        'durasi' => $durasi,
        'status' => 'pending',
    ]);

    return response()->json([
        'message' => 'Pengajuan izin berhasil dibuat.',
        'data' => $izin
    ], 201);
}


    // Menampilkan izin berdasarkan ID
    public function show($id)
    {
        $izin = Izin::where('id', $id)->where('id_karyawan', Auth::id())->firstOrFail();
        return response()->json($izin);
    }

    // Mengupdate izin
    public function update(Request $request, $id)
{
    $izin = Izin::where('id', $id)->where('id_karyawan', Auth::id())->firstOrFail();
    
    // Validasi input
    $request->validate([
        'tgl_mulai' => 'required|date',
        'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
        'alasan' => 'required|string',
        'keterangan' => 'nullable|string',
        'status' => 'in:pending,approved,declined',
    ]);

    // Hitung durasi dari tgl_mulai dan tgl_selesai
    $durasi = now()->parse($request->tgl_selesai)->diffInDays($request->tgl_mulai) + 1;

    // Logika untuk membatasi hak akses
    if (Auth::user()->is_admin) {
        // Admin dapat memperbarui status
        $izin->update([
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'alasan' => $request->alasan,
            'keterangan' => $request->keterangan,
            'durasi' => $durasi,
            'status' => $request->status,
        ]);
    } else {
        // Non-admin tidak dapat mengubah status
        $izin->update([
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'alasan' => $request->alasan,
            'keterangan' => $request->keterangan,
            'durasi' => $durasi,
            // Status tetap tidak berubah
        ]);
    }

    return response()->json($izin);
}


    // Menghapus izin
    public function destroy($id)
    {
        $izin = Izin::where('id', $id)->where('id_karyawan', Auth::id())->firstOrFail();
        $izin->delete();
        return response()->json(['message' => 'Izin deleted successfully']);
    }
}