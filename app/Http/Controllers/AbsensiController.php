<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Absensi;

class AbsensiController extends Controller
{
    /**
     * Fungsi untuk mendapatkan alamat dari koordinat GPS (latitude, longitude)
     * menggunakan Google Maps Geocoding API
     */
    public function getAddressFromCoordinates($latitude, $longitude)
    {
        $client = new Client();
        $url = "https://nominatim.openstreetmap.org/reverse";

        try {
            $response = $client->get($url, [
                'query' => [
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'format' => 'json',
                    'addressdetails' => 1,
                ],
                'headers' => [
                    'User-Agent' => 'login_signup/1.0' // Ganti dengan nama aplikasi Anda
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            if (isset($data['display_name'])) {
                return $data['display_name'];
            } else {
                return "Alamat tidak ditemukan";
            }
        } catch (\Exception $e) {
            return "Terjadi kesalahan: " . $e->getMessage();
        }
    }

    /**
     * Fungsi untuk absen masuk
     */
    public function absenMasuk(Request $request)
    {
        // Validasi data request
        $request->validate([
            'id_user' => 'required|integer',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'foto_masuk' => 'nullable|image|max:2048', // Validasi untuk foto
        ]);

        // Ambil latitude dan longitude dari request
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        // Dapatkan alamat dari reverse geocoding
        $alamat = $this->getAddressFromCoordinates($latitude, $longitude);

        // Simpan data absensi masuk ke dalam database
        $absensi = new Absensi();
        $absensi->id_user = $request->input('id_user');
        $absensi->tanggal = now()->format('Y-m-d');
        $absensi->jam_masuk = now()->format('H:i:s');

        // Upload foto masuk
        if ($request->hasFile('foto_masuk')) {
            $file = $request->file('foto_masuk');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/foto_masuk'), $fileName);
            $absensi->foto_masuk = 'uploads/foto_masuk/' . $fileName;
        }

        $absensi->lokasi_masuk = $alamat; // Simpan alamat hasil reverse geocoding
        $absensi->save();

        return response()->json([
            'message' => 'Absen masuk berhasil',
            'data' => $absensi
        ], 200);
    }

    /**
     * Fungsi untuk absen keluar
     */
    public function absenKeluar(Request $request)
    {
        // Validasi data request
        $request->validate([
            'id_user' => 'required|integer',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'foto_keluar' => 'nullable|image|max:2048', // Validasi untuk foto keluar
        ]);

        // Ambil latitude dan longitude dari request
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        // Dapatkan alamat dari reverse geocoding
        $alamat = $this->getAddressFromCoordinates($latitude, $longitude);

        // Cari data absensi hari ini berdasarkan id_user dan tanggal
        $absensi = Absensi::where('id_user', $request->input('id_user'))
                    ->whereDate('tanggal', now()->format('Y-m-d'))
                    ->first();

        if (!$absensi) {
            return response()->json(['message' => 'Data absensi tidak ditemukan'], 404);
        }

        // Update data absensi keluar
        $absensi->jam_keluar = now()->format('H:i:s');

        // Upload foto keluar
        if ($request->hasFile('foto_keluar')) {
            $file = $request->file('foto_keluar');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/foto_keluar'), $fileName);
            $absensi->foto_keluar = 'uploads/foto_keluar/' . $fileName;
        }

        $absensi->lokasi_keluar = $alamat; // Simpan alamat hasil reverse geocoding
        $absensi->save();

        return response()->json([
            'message' => 'Absen keluar berhasil',
            'data' => $absensi
        ], 200);
    }

    public function riwayatAbsensi(Request $request)
{
    $request->validate([
        'id_user' => 'required|integer',
    ]);

    $idUser = $request->input('id_user');
    $riwayat = Absensi::where('id_user', $idUser)->orderBy('tanggal', 'desc')->get();

    return response()->json([
        'message' => 'Riwayat absensi berhasil diambil',
        'data' => $riwayat
    ], 200);
}

}
