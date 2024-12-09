<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika berbeda dari nama default
    protected $table = 'tasks';

    // Tentukan primary key
    protected $primaryKey = 'id_tugas';

    // Tentukan tipe primary key
    public $incrementing = true;  // Jika auto increment
    protected $keyType = 'int';   // Pastikan tipe data adalah integer

    protected $fillable = [
        'id_karyawan',
        'judul_proyek',
        'kegiatan',
        'status',
        'tgl_mulai',
        'batas_penyelesaian',
        'tgl_selesai',
    ];

    // Relasi ke model Karyawan
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }
}
