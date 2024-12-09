<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Izin extends Model
{
    use HasFactory;
    
    protected $table = 'izin';
    protected $fillable = ['id_karyawan', 'tgl_mulai', 'tgl_selesai', 'alasan', 'keterangan', 'durasi', 'status'];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan');
    }

    // Menghitung durasi hanya untuk akses, bukan untuk penyimpanan
    public function getDurasiAttribute()
    {
        $tglMulai = Carbon::parse($this->tgl_mulai);
        $tglSelesai = Carbon::parse($this->tgl_selesai);
        return $tglSelesai->diffInDays($tglMulai) + 1; // Tambah 1 hari karena hari mulai dihitung
    }
}