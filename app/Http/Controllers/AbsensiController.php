<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Izin;
use App\Models\DinasLuarKota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;



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

public function absenMasuk(Request $request)
{
    $validatedData = $request->validate([
        'tanggal' => 'required|date',
        'jam_masuk' => 'required|date_format:H:i:s',
        'foto_masuk' => 'required|string',
        'latitude_masuk' => 'nullable|regex:/^-?\d{1,3}\.\d+$/',
        'longitude_masuk' => 'nullable|regex:/^-?\d{1,3}\.\d+$/',
        'lokasi_masuk' => 'nullable|string',
    ]);
    
    
    // Ambil id_karyawan dari pengguna yang sedang login
    $validatedData['id_karyawan'] = auth()->user()->id;

    // Format tanggal yang benar menggunakan Carbon
    $tanggal = Carbon::parse($validatedData['tanggal'])->format('Y-m-d');

    // Cek apakah sudah ada absensi untuk tanggal tersebut
    $existingAbsensi = Absensi::where('id_karyawan', $validatedData['id_karyawan'])
                              ->whereDate('tanggal', $tanggal)
                              ->exists();

    // Cek apakah sudah ada izin (cuti) untuk tanggal tersebut
    $existingIzin = Izin::where('id_karyawan', $validatedData['id_karyawan'])
                        ->where(function($query) use ($tanggal) {
                            // Cek apakah tanggal absensi berada dalam rentang izin (tgl_mulai sampai tgl_selesai)
                            $query->whereBetween(DB::raw('DATE(tgl_mulai)'), [$tanggal, $tanggal])
                                  ->orWhereBetween(DB::raw('DATE(tgl_selesai)'), [$tanggal, $tanggal])
                                  ->orWhere(function($query) use ($tanggal) {
                                      $query->whereDate('tgl_mulai', '<=', $tanggal)
                                            ->whereDate('tgl_selesai', '>=', $tanggal);
                                  });
                        })
                        ->exists(); 

    // Cek apakah sudah ada dinas luar kota untuk tanggal tersebut
    $existingDinasLuarKota = DinasLuarKota::where('id_karyawan', $validatedData['id_karyawan'])
                                           ->where(function($query) use ($tanggal) {
                                               // Cek apakah tanggal absensi berada dalam rentang dinas luar kota (tgl_berangkat sampai tgl_kembali)
                                               $query->whereBetween(DB::raw('DATE(tgl_berangkat)'), [$tanggal, $tanggal])
                                                     ->orWhereBetween(DB::raw('DATE(tgl_kembali)'), [$tanggal, $tanggal])
                                                     ->orWhere(function($query) use ($tanggal) {
                                                         $query->whereDate('tgl_berangkat', '<=', $tanggal)
                                                               ->whereDate('tgl_kembali', '>=', $tanggal);
                                                     });
                                           })
                                           ->exists(); 

    // Jika sudah ada absensi, izin, atau dinas luar kota pada tanggal tersebut
    if ($existingAbsensi) {
        return response()->json([
            'message' => 'Anda sudah melakukan absensi pada tanggal ini.'
        ], 400);
    }

    if ($existingIzin) {
        return response()->json([
            'message' => 'Anda sudah mengajukan izin (cuti) pada tanggal ini.'
        ], 400);
    }

    if ($existingDinasLuarKota) {
        return response()->json([
            'message' => 'Anda sudah memiliki jadwal dinas luar kota pada tanggal ini.'
        ], 400);
    }

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
        $apiKey = env('GOOGLE_MAPS_API_KEY'); // Menggunakan key yang benar dari .env


        $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json?latlng={$latitude},{$longitude}&key={$apiKey}");

        if ($response->successful() && isset($response['results'][0]['formatted_address'])) {
            return $response['results'][0]['formatted_address'];
        }

        return null;
    }

    
}
