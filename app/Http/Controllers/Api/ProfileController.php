<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\JabatanController;

class ProfileController extends Controller
{
    public function index()
{
    $employee = Auth::guard('api')->user();
    
    // Mengambil nama jabatan dari relasi
    $jabatan = $employee->jabatan ? $employee->jabatan->jabatan : null;

    return response()->json([
        'success' => true,
        'profile' => [
            'nip' => $employee->nip,  // Menambahkan NIP
            'nik' => $employee->nik,  // Menambahkan NIK
            'nama_karyawan' => $employee->nama_karyawan,
            'email' => $employee->email,
            'no_handphone' => $employee->no_handphone,
            'alamat' => $employee->alamat,
            'avatar_url' => $employee->avatar ? asset('storage/' . $employee->avatar) : null,
            'nama_jabatan' => $jabatan ?? 'Tidak Ada Jabatan',

        ],
    ]);
}



    public function update(Request $request)
    {
        // Validasi data input
        $request->validate([
            
            'nama_karyawan' => 'required|string|max:255',
            'no_handphone' => 'required|string|max:15',
            'alamat' => 'required|string|max:255',

        ]);

        try {
            // Ambil karyawan yang sedang login
            $karyawan = Auth::guard('api')->user();
            // Update data karyawan
            
            $karyawan->nama_karyawan = $request->input('nama_karyawan');
            $karyawan->no_handphone = $request->input('no_handphone');
            $karyawan->alamat = $request->input('alamat');
            $karyawan->save();

            // Kembalikan respons sukses
            return response()->json([
                'status' => 'success',
                'message' => 'Profile update d successfully',
                'profile' => $karyawan,
            ], 200);
        } catch (\Exception $e) {
            // Jika terjadi error, kembalikan respons error
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update profile',
                'error' => $e->getMessage(),
            ], 500);
        } 
    }

    public function uploadAvatar(Request $request)
{
    $request->validate([
        'avatar' => 'required|image|max:2048', // Validasi file
    ]);

    $path = $request->file('avatar')->store('avatars', 'public');
    $user = auth()->user();
    $user->avatar = $path;
    $user->save();

    return response()->json(['avatar_url' => asset('storage/' . $path)], 200);
}



   
}