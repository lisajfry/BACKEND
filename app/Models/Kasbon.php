<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kasbon extends Model
{
    use HasFactory;

    protected $table = 'kasbons';

    protected $fillable = [
        'id_karyawan',
        'tanggal_pengajuan',
        'jumlah_kasbon',
        'keterangan',
        'status',
    ];

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'kasbon_id'); // Relasi ke tabel pembayaran
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan'); // Relasi ke tabel karyawan
    }
}
