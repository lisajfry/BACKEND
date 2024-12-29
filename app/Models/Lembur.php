<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lembur extends Model
{
    use HasFactory;

    protected $table = 'lembur';

    protected $fillable = [
        'id_karyawan',
        'tanggal_lembur',
        'jam_mulai',
        'jam_selesai',
        'durasi_lembur',
        'alasan_lembur',
    ];
    

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan');
    }
}