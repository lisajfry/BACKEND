<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Karyawan extends Authenticatable implements JWTSubject
{
    use Notifiable;

    // Kolom-kolom yang boleh diisi secara massal
    protected $fillable = [
        'nama_karyawan', 'nik', 'nip', 'email', 'no_handphone', 'alamat', 'password', 'device_code', 'avatar', 'jabatan_id'
    ];

    // Kolom-kolom yang disembunyikan dari representasi array atau JSON
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Mengambil identifier utama dari JWT
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Menambahkan klaim kustom ke token JWT (jika ada)
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Relasi antara karyawan dan absensi
     */
    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    /**
     * Relasi antara karyawan dan jabatan
     */
    // Model Karyawan.php
public function jabatan()
{
    return $this->belongsTo(Jabatan::class, 'jabatan_id'); // Asumsi kolom foreign key-nya adalah 'id_jabatan'
}

}
