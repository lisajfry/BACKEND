<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    // Menampilkan daftar tugas berdasarkan karyawan yang login
    public function index()
    {
        // Ambil tugas berdasarkan id_karyawan yang terhubung dengan user yang login
        $tasks = Task::where('id_karyawan', Auth::id())->get();
        return response()->json($tasks);
    }

    // Menampilkan detail tugas berdasarkan id
    public function show($id)
    {
        // Ambil tugas berdasarkan id_karyawan yang terhubung dengan user yang login
        $task = Task::where('id_karyawan', Auth::id())->findOrFail($id);
        return response()->json($task);
    }

    // Membuat tugas baru
    public function store(Request $request)
    {
        $request->validate([
            'judul_proyek' => 'required|string|max:255',
            'kegiatan' => 'required|string',
            'status' => 'required|in:belum dimulai,dalam progres,selesai',
            'tgl_mulai' => 'required|date',
            'batas_penyelesaian' => 'required|date',
            'tgl_selesai' => 'nullable|date', // Menambahkan validasi nullable untuk tgl_selesai
        ]);

        $task = Task::create([
            'judul_proyek' => $request->judul_proyek,
            'kegiatan' => $request->kegiatan,
            'status' => $request->status,
            'tgl_mulai' => $request->tgl_mulai,
            'batas_penyelesaian' => $request->batas_penyelesaian,
            'tgl_selesai' => $request->tgl_selesai, // Menambahkan tgl_selesai jika ada
            'id_karyawan' => Auth::id(), // Menambahkan id_karyawan sesuai user yang login
        ]);

        return response()->json($task, 201);
    }

    // Mengupdate tugas berdasarkan id
    public function update(Request $request, $id) 
{
    $request->validate([
        'judul_proyek' => 'required|string|max:255',
        'kegiatan' => 'required|string',
        'status' => 'required|in:belum dimulai,dalam progres,selesai',
        'tgl_mulai' => 'required|date',
        'batas_penyelesaian' => 'required|date',
        'tgl_selesai' => 'nullable|date', // Menambahkan validasi nullable untuk tgl_selesai
    ]);

    // Pastikan query di sini menggunakan id_tugas
    $task = Task::where('id_karyawan', Auth::id())->where('id_tugas', $id)->firstOrFail();

    $task->update([
        'judul_proyek' => $request->judul_proyek,
        'kegiatan' => $request->kegiatan,
        'status' => $request->status,
        'tgl_mulai' => $request->tgl_mulai,
        'batas_penyelesaian' => $request->batas_penyelesaian,
        'tgl_selesai' => $request->tgl_selesai, // Menambahkan tgl_selesai jika ada
    ]);

    return response()->json($task);
}


    // Menghapus tugas berdasarkan id
    public function destroy($id)
    {
        $task = Task::where('id_karyawan', Auth::id())->findOrFail($id);
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }
}
