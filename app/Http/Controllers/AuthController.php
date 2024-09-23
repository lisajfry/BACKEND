<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Melakukan validasi data input pengguna sebelum registrasi
        $this->validate($request, [
            'nama_karyawan' => 'required|string|max:255',
            'nik' => 'required|string|max:20|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'no_handphone' => 'required|string|max:15',
            'alamat' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Membuat pengguna baru dengan data yang sudah divalidasi
        $user = User::create([
            'nama_karyawan' => $request->nama_karyawan,
            'nik' => $request->nik,
            'email' => $request->email,
            'no_handphone' => $request->no_handphone,
            'alamat' => $request->alamat,
            'password' => Hash::make($request->password),
        ]);

        // Membuat token JWT untuk pengguna yang baru dibuat
        $token = JWTAuth::fromUser($user);

        // Mengembalikan respons JSON berisi data pengguna dan token JWT dengan status HTTP 201 (Created)
        return response()->json(compact('user', 'token'), 201);
    }

    public function login(Request $request)
    {
        // Mengambil email dan password dari input request
        $credentials = $request->only('email', 'password');

        // Mencoba melakukan login menggunakan JWTAuth dengan kredensial yang diberikan
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Terjadi kesalahan'], 401);
        }

        // Jika login berhasil, kembalikan token JWT dalam format JSON
        return response()->json(compact('token'));
    }

    public function logout()
    {
        // Mengeluarkan (logout) pengguna yang sedang login
        Auth::logout();
        // Mengembalikan respons JSON dengan pesan berhasil logout
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function getUser(Request $request)
    {
        // Mengambil data pengguna yang sedang login berdasarkan token JWT yang diterima
        $user = JWTAuth::parseToken()->authenticate();

        // Jika pengguna tidak ditemukan, kembalikan respons error User Not Found (404)
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Jika pengguna ditemukan, kembalikan data pengguna dalam format JSON
        return response()->json(compact('user'), 200);
    }
}
