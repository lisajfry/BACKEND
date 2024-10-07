<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Melakukan validasi data input pengguna sebelum registrasi
        $this->validate($request, [
            'nama_karyawan' => 'required|string|max:255',
            'nik' => 'required|string|max:20|unique:karyawans',
            'email' => 'required|string|email|max:255|unique:karyawans',
            'no_handphone' => 'required|string|max:15',
            'alamat' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Membuat pengguna baru dengan data yang sudah divalidasi
        $karyawan = Karyawan::create([
            'nama_karyawan' => $request->nama_karyawan,
            'nik' => $request->nik,
            'email' => $request->email,
            'no_handphone' => $request->no_handphone,
            'alamat' => $request->alamat,
            'password' => Hash::make($request->password),
        ]);

        // Membuat token JWT untuk pengguna yang baru dibuat
        $token = JWTAuth::fromUser($karyawan);

        return response()->json(compact('karyawan', 'token'), 201);
    }

    public function login(Request $request)
    {
        // Validasi input login
        $this->validate($request, [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'device_code' => 'required|string', // Device code should be sent with the request
        ]);

        // Ambil data email dan password
        $credentials = $request->only('email', 'password');

        // Mencari karyawan berdasarkan email
        $karyawan = Karyawan::where('email', $request->email)->first();

        // Cek apakah karyawan ditemukan
        if (!$karyawan) {
            return response()->json(['error' => 'Karyawan not found'], 404);
        }

        // Cek apakah device_code sudah terdaftar
        if ($karyawan->device_code && $karyawan->device_code !== $request->device_code) {
            return response()->json(['error' => 'This account is already linked to another device'], 403);
        }

        // Jika device_code belum terdaftar, simpan device_code
        if (!$karyawan->device_code) {
            $karyawan->device_code = $request->device_code;
            $karyawan->save();
        }

        // Coba login dengan JWTAuth
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json(compact('token'));
    }

    public function logout()
    {
        // Invalidate token saat logout
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function getKaryawan(Request $request)
    {
        $karyawan = JWTAuth::parseToken()->authenticate();

        if (!$karyawan) {
            return response()->json(['message' => 'Karyawan not found'], 404);
        }

        return response()->json(compact('karyawan'), 200);
    }
}
