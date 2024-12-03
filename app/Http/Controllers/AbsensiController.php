<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AbsensiController extends Controller
{
    // Metode index tetap sama
    public function index()
{
    // Ambil ID karyawan yang sedang login
    $idKaryawan = auth()->user()->id;

    // Ambil data absensi berdasarkan ID karyawan yang sedang login
    $absensi = Absensi::where('id_karyawan', $idKaryawan)->get();

    return response()->json($absensi);
}


    // Metode untuk absen masuk
    public function absenMasuk(Request $request)
{
    $validatedData = $request->validate([
        'tanggal' => 'required|date',
        'jam_masuk' => 'required|date_format:H:i:s',
        'foto_masuk' => 'required|string',
        'latitude_masuk' => 'nullable|numeric',
        'longitude_masuk' => 'nullable|numeric',
        'lokasi_masuk' => 'nullable|string',
    ]);

    // Ambil id_karyawan dari pengguna yang sedang login
    $validatedData['id_karyawan'] = auth()->user()->id;

    // Tentukan jam batas terlambat
    $jamMasukBatas = '08:00:00';

    // Periksa apakah jam masuk terlambat atau tepat waktu
    if ($validatedData['jam_masuk'] > $jamMasukBatas) {
        $validatedData['status'] = 'terlambat';
    } else {
        $validatedData['status'] = 'tepat_waktu';
    }

    // Jika koordinat tersedia, lakukan reverse geocoding
    if (!empty($validatedData['latitude_masuk']) && !empty($validatedData['longitude_masuk'])) {
        $alamat = $this->getAlamatDariKoordinat($validatedData['latitude_masuk'], $validatedData['longitude_masuk']);
        $validatedData['lokasi_masuk'] = $alamat;
    }

    // Simpan data absensi
    $absensi = Absensi::create($validatedData);

    return response()->json($absensi, 201);
}


    // Metode reverse geocoding menggunakan API eksternal untuk mendapatkan alamat dari koordinat
    protected function getAlamatDariKoordinat($latitude, $longitude)
    {
        $apiKey = env('YOUR_GOOGLE_MAPS_API_KEY'); // Ganti dengan API key Anda

        $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&key={$apiKey}");

        if ($response->successful() && isset($response['results'][0]['formatted_address'])) {
            return $response['results'][0]['formatted_address'];
        }

        return null;
    }

    public function absenKeluar(Request $request)
    {
        $validatedData = $request->validate([
            'id_karyawan' => 'required|exists:karyawans,id',
            'tanggal' => 'required|date',
            'jam_keluar' => 'required|date_format:H:i:s',
            'foto_keluar' => 'nullable|string',
            'latitude_keluar' => 'nullable|numeric',
            'longitude_keluar' => 'nullable|numeric',
            'lokasi_keluar' => 'nullable|string',
        ]);

        $absensi = Absensi::where('id_karyawan', $validatedData['id_karyawan'])
            ->where('tanggal', $validatedData['tanggal'])
            ->firstOrFail();

        $absensi->update($validatedData);
        return response()->json($absensi);
    }
}
