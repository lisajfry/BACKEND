<?php

namespace App\Http\Controllers;

use App\Models\LokasiKantor;
use Illuminate\Http\Request; 

class LokasiKantorController extends Controller
{
    // Menampilkan daftar semua lokasi kantor
    public function index()
    {
        $lokasi_kantor = LokasiKantor::all();
        return response()->json($lokasi_kantor);
    }

    // Menyimpan lokasi kantor baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_kantor' => 'required|string|max:255',
            'alamat' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $lokasi_kantor = LokasiKantor::create($request->all());
        return response()->json(['message' => 'Lokasi kantor berhasil ditambahkan', 'data' => $lokasi_kantor], 201);
    }

    // Menampilkan detail lokasi kantor berdasarkan ID
    public function show($id)
    {
        $lokasi_kantor = LokasiKantor::findOrFail($id);
        return response()->json($lokasi_kantor);
    }

    // Memperbarui data lokasi kantor
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kantor' => 'required|string|max:255',
            'alamat' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $lokasi_kantor = LokasiKantor::findOrFail($id);
        $lokasi_kantor->update($request->all());
        return response()->json(['message' => 'Lokasi kantor berhasil diperbarui', 'data' => $lokasi_kantor]);
    }

    // Menghapus lokasi kantor
    public function destroy($id)
    {
        $lokasi_kantor = LokasiKantor::findOrFail($id);
        $lokasi_kantor->delete();
        return response()->json(['message' => 'Lokasi kantor berhasil dihapus']);
    }
}
