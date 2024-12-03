<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    // Menampilkan daftar jabatan
    public function index()
    {
        $jabatan = Jabatan::all();
        return response()->json($jabatan);
    }

    // Menyimpan data jabatan baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:100',
            'gaji_pokok' => 'required|numeric|min:0',
        ]);

        $jabatan = Jabatan::create([
            'nama_jabatan' => $request->nama_jabatan,
            'gaji_pokok' => $request->gaji_pokok,
        ]);
 
        return response()->json($jabatan, 201);
    }

    // Menampilkan data jabatan tertentu
    public function show($id)
    {
        $jabatan = Jabatan::find($id);

        if (!$jabatan) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($jabatan);
    }

    // Mengupdate data jabatan
    public function update(Request $request, $id)
    {
        $jabatan = Jabatan::find($id);

        if (!$jabatan) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $request->validate([
            'nama_jabatan' => 'sometimes|string|max:100',
            'gaji_pokok' => 'sometimes|numeric|min:0',
        ]);

        $jabatan->update($request->only(['nama_jabatan', 'gaji_pokok']));

        return response()->json($jabatan);
    }

    // Menghapus data jabatan
    public function destroy($id)
    {
        $jabatan = Jabatan::find($id);

        if (!$jabatan) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $jabatan->delete();

        return response()->json(['message' => 'Data berhasil dihapus']);
    }
}
