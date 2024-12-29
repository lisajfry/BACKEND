<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Izin;
use App\Models\DinasLuarKota;
use App\Models\Absensi; // Tambahkan model Absensi
use App\Models\Lembur; // Tambahkan model Absensi
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

                        $lemburHours = Lembur::where('id_karyawan', $user->id)
                        ->whereMonth('tanggal_lembur', $currentMonth)  // Ganti 'tanggal' dengan 'date'
                        ->whereYear('tanggal_lembur', $currentYear)    // Ganti 'tanggal' dengan 'date'
                        ->sum('durasi_lembur');
                    

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
public function generateSlipGaji(Request $request)
{
    try {
        // Ambil user saat ini
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Pastikan karyawan memiliki jabatan
        $jabatan = $user->jabatan; // Relasi ke tabel Jabatan
        if (!$jabatan) {
            return response()->json(['error' => 'Jabatan tidak ditemukan untuk karyawan ini'], 404);
        }

        // Panggil fungsi untuk mendapatkan rekap payroll summary
        $payrollSummary = $this->getPayrollSummary()->getData();

        if (isset($payrollSummary->error)) {
            return response()->json(['error' => $payrollSummary->error], 500);
        }


        // Hitung bonus lembur
        $lembur = $payrollSummary->lembur_count ?? 0; // Jam lembur
        $bonus = $jabatan->bonus ?? 0; // Nilai bonus per jam lembur
        $total_bonus = $lembur * $bonus;

        
        // Menghitung total kehadiran (kehadiran + dinas luar kota)
        $kehadiran = $payrollSummary->kehadiran_count ?? 0; // Ambil data jumlah kehadiran

        $dinasLuarKota = $payrollSummary->dinas_luar_kota_count ?? 0; // Ambil data dinas luar kota
        $total_kehadiran = $kehadiran + $dinasLuarKota;


        // Hitung uang kehadiran
$uang_kehadiran_perhari = $jabatan->uang_kehadiran_perhari ?? 0; // Ambil nilai uang kehadiran per hari dari jabatan
$uang_kehadiran = $total_kehadiran * $uang_kehadiran_perhari; // Hitung uang kehadiran berdasarkan total kehadiran




         // Hitung uang makan
$uang_makan_perhari = $jabatan->uang_makan ?? 0; // Ambil nilai uang makan per hari dari jabatan
$uang_makan = $total_kehadiran * $uang_makan_perhari; // Hitung total uang makan berdasarkan total kehadiran


        // Data untuk PDF
        $data = [
            'nama_karyawan' => $user->nama_karyawan, // Pastikan kolom 'nama_karyawan' ada di tabel
            'nama_jabatan' => $jabatan->jabatan ?? 'Tidak Ada Jabatan',
            'bulan' => Carbon::now()->format('F'),
            'tahun' => Carbon::now()->year,
            'izin' => $payrollSummary->izin_count ?? 0,
            'cuti' => $payrollSummary->cuti_count ?? 0,
            'dinas_luar_kota' => $payrollSummary->dinas_luar_kota_count ?? 0,
            'kehadiran' => $kehadiran,
            'total_kehadiran' => $total_kehadiran, // Menambahkan total kehadiran
            'lembur' => $payrollSummary->lembur_count ?? 0,
            'uang_kehadiran' => $uang_kehadiran,
            'uang_kehadiran_perhari' => $uang_kehadiran_perhari,
            'uang_makan' => $uang_makan,
            'uang_makan_perhari' => $uang_makan_perhari,
            'gaji_pokok' => $jabatan->gaji_pokok ?? 0,
            'tunjangan' => $jabatan->tunjangan ?? 0,
            'bonus' => $jabatan->bonus ?? 0,
            'total_bonus' => $total_bonus,
            'potongan' => $jabatan->potongan ?? 0,
        ];

        // Generate PDF
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('pdf.slip_gaji', $data);

        // Simpan atau tampilkan PDF
        return $pdf->stream("slip_gaji_{$user->nama_karyawan}_{$data['bulan']}_{$data['tahun']}.pdf");
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


}