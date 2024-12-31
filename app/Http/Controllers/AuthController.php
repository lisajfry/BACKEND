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
        'nip' => 'required|string|max:20|unique:karyawans',
        'email' => 'required|string|email|max:255|unique:karyawans',
        'no_handphone' => 'required|string|max:15',
        'alamat' => 'required|string',
        'password' => 'required|string|min:6|confirmed',
        'device_code' => 'required|string', // Device code harus dikirim bersama request
        'fingerprint_template' => 'nullable|string', // Validasi fingerprint template jika ada
    ]);

    // Cek apakah device_code sudah digunakan oleh karyawan lain
    $existingDevice = Karyawan::where('device_code', $request->device_code)->first();
    if ($existingDevice) {
        return response()->json(['error' => 'Perangkat ini sudah terdaftar untuk karyawan lain'], 403);
    }

    // Mencari karyawan berdasarkan email atau nomor handphone
    $karyawan = Karyawan::where('email', $request->email)
                        ->orWhere('no_handphone', $request->no_handphone)
                        ->first();

    // Jika karyawan ditemukan
    if ($karyawan) {
        // Jika device_code sudah terdaftar dan berbeda
        if ($karyawan->device_code && $karyawan->device_code !== $request->device_code) {
            return response()->json(['error' => 'Akun ini sudah terhubung dengan perangkat lain'], 403);
        }

        // Perbarui device_code jika belum terdaftar
        if (!$karyawan->device_code) {
            $karyawan->device_code = $request->device_code;
            // Simpan fingerprint jika diberikan
            if ($request->has('fingerprint_template')) {
                $karyawan->fingerprint_template = $request->fingerprint_template;
            }
            $karyawan->save();
        }

        

        // Membuat token JWT untuk karyawan yang sudah ada
        $token = JWTAuth::fromUser($karyawan);

        return response()->json(compact('karyawan', 'token'), 200);
    }

    // Jika karyawan belum terdaftar, buat karyawan baru
    $karyawan = Karyawan::create([
        'nama_karyawan' => $request->nama_karyawan,
        'nik' => $request->nik,
        'nip' => $request->nip,
        'email' => $request->email,
        'no_handphone' => $request->no_handphone,
        'alamat' => $request->alamat,
        'password' => Hash::make($request->password),
        'device_code' => $request->device_code,
        'fingerprint_template' => $request->fingerprint_template, // Menyimpan template fingerprint jika ada
    ]);

    // Membuat token JWT untuk karyawan yang baru dibuat
    $token = JWTAuth::fromUser($karyawan);

    return response()->json(compact('karyawan', 'token'), 201);
}


public function login(Request $request)
{
    // Validasi input login
    $this->validate($request, [
        'email' => 'required|string|email',
        'password' => 'required|string',
        'device_code' => 'required|string', // Device code harus dikirim bersama request
        'fingerprint_template' => 'nullable|string', // Jika fingerprint template dikirimkan
    ]);

    // Ambil data email dan password
    $credentials = $request->only('email', 'password');

    // Mencari karyawan berdasarkan email atau nomor handphone
    $karyawan = Karyawan::where('email', $request->email)
                        ->orWhere('no_handphone', $request->no_handphone)
                        ->first();

    // Cek apakah karyawan ditemukan
    if (!$karyawan) {
        return response()->json(['error' => 'Karyawan tidak ditemukan'], 404);
    }

    // Cek apakah device_code sudah terdaftar dan berbeda
    if ($karyawan->device_code && $karyawan->device_code !== $request->device_code) {
        return response()->json(['error' => 'Akun ini sudah terhubung dengan perangkat lain'], 403);
    }

    // Jika device_code belum terdaftar, simpan device_code
    if (!$karyawan->device_code) {
        $karyawan->device_code = $request->device_code;
        $karyawan->save();
    }

    // Coba login dengan JWTAuth
    if (!$token = JWTAuth::attempt($credentials)) {
        return response()->json(['error' => 'Kredensial tidak valid'], 401);
    }

    // Jika ada fingerprint_template yang dikirimkan, lakukan pengecekan
    if ($request->has('fingerprint_template')) {
        if ($karyawan->fingerprint_template !== $request->fingerprint_template) {
            return response()->json(['error' => 'Fingerprint tidak cocok'], 401);
        }
    }

    return response()->json(compact('token'));
}


    public function logout()
    {
        // Invalidate token saat logout
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Berhasil logout']);
    }

    public function getKaryawan(Request $request)
    {
        $karyawan = JWTAuth::parseToken()->authenticate();

        if (!$karyawan) {
            return response()->json(['message' => 'Karyawan tidak ditemukan'], 404);
        }

        return response()->json(compact('karyawan'), 200);
    }
}
