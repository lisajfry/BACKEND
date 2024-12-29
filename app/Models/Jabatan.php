<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;
    
        protected $table = 'jabatan';
    
        protected $fillable = [
            'nama_jabatan',
            'gaji_pokok',
            'uang_kehadiran_per_hari',
            'uang_makan',
            'bonus',
            'tunjangan',
            'potongan',
        ];
    
    
    // Model Jabatan.php
public function karyawans()
{
    return $this->hasMany(Karyawan::class, 'jabatan_id');
}

}
