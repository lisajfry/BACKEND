<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Izin;
use App\Models\DinasLuarKota;
use App\Models\Absensi; // Tambahkan model Absensi
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PayrollController extends Controller
{
    // Fungsi untuk menghitung hari kerja dalam rentang tanggal (tidak termasuk Minggu dan hari libur nasional)
    protected function calculateWorkingDays($startDate, $endDate, $holidays = [])
    {
        $workingDays = 0;
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            // Hitung hanya jika bukan hari Minggu dan bukan hari libur nasional
            if (!$currentDate->isSunday() && !in_array($currentDate->format('Y-m-d'), $holidays)) {
                $workingDays++;
            }
            $currentDate->addDay();
        }
         
        return $workingDays;
    }

    // Mendapatkan rekap jumlah izin, cuti, dan dinas luar kota serta kehadiran selama satu bulan
    public function getPayrollSummary()
{
    try {
        $user = Auth::user();
        
        // Mendapatkan bulan dan tahun saat ini
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Daftar hari libur nasional
        $holidays = [
            '2024-01-01', // Tahun Baru
            '2024-03-11', // Hari Raya Nyepi
            '2024-05-01', // Hari Buruh
            // Tambahkan hari libur lainnya di sini
        ];

       // Hitung jumlah hari izin dalam bulan ini
       $izinDays = Izin::where('id_karyawan', $user->id)
       ->where('alasan', 'IZIN')
       ->whereMonth('tgl_mulai', $currentMonth)
       ->whereYear('tgl_mulai', $currentYear)
       ->get()
       ->sum(fn ($izin) => $this->calculateWorkingDays(
           Carbon::parse($izin->tgl_mulai),
           Carbon::parse($izin->tgl_selesai),
           $holidays
       ));


        // Menghitung durasi dinas luar kota dalam bulan ini
        $dinasLuarKotaDays = DinasLuarKota::where('id_karyawan', $user->id)
                        ->whereMonth('tgl_berangkat', $currentMonth)
                        ->whereYear('tgl_berangkat', $currentYear)
                        ->get()
                        ->sum(function ($dinas) use ($holidays) {
                            return $this->calculateWorkingDays(
                                Carbon::parse($dinas->tgl_berangkat),
                                Carbon::parse($dinas->tgl_kembali),
                                $holidays
                            );
                        });

          // Hitung jumlah hari cuti dalam bulan ini
          $cutiDays = Izin::where('id_karyawan', $user->id)
          ->where('alasan', 'CUTI')
          ->whereMonth('tgl_mulai', $currentMonth)
          ->whereYear('tgl_mulai', $currentYear)
          ->get()
          ->sum(fn ($cuti) => $this->calculateWorkingDays(
              Carbon::parse($cuti->tgl_mulai),
              Carbon::parse($cuti->tgl_selesai),
              $holidays
          ));


        // Menghitung jumlah kehadiran unik (berdasarkan tanggal unik) dalam bulan ini
        $kehadiranCount = Absensi::where('id_karyawan', $user->id)
                        ->whereMonth('tanggal', $currentMonth)
                        ->whereYear('tanggal', $currentYear)
                        ->distinct('tanggal') // Menghitung tanggal unik
                        ->count('tanggal'); // Menghitung jumlah tanggal unik

        // Menghitung total jam lembur
        $lemburHours = Absensi::where('id_karyawan', $user->id)
                        ->whereMonth('tanggal', $currentMonth)
                        ->whereYear('tanggal', $currentYear)
                        ->get()
                        ->sum(function ($absen) {
                            $jamKeluar = Carbon::parse($absen->jam_keluar);
                            $batasJamLembur = Carbon::parse('17:00:00');

                            if ($jamKeluar->greaterThan($batasJamLembur)) {
                                return $jamKeluar->diffInHours($batasJamLembur);
                            }
                            return 0;
                        });

        return response()->json([
            'izin_count' => $izinDays,
            'dinas_luar_kota_count' => $dinasLuarKotaDays,
            'cuti_count' => $cutiDays,
            'kehadiran_count' => $kehadiranCount,
            'lembur_count' => $lemburHours, // Hasil perhitungan lembur
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }

}

}
