<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    // Tentukan tabel yang akan dihubungkan oleh model ini
    protected $table = 'absensi';

    // Field yang boleh diisi secara massal
    protected $fillable = [
        'id_user',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'foto_masuk',
        'foto_keluar',
        'lokasi_masuk',
        'lokasi_keluar',
    ];

    // Definisikan hubungan ke model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
