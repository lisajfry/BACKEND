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
        'nama_karyawan', 'nik', 'nip', 'email', 'no_handphone', 'alamat', 'password', 'device_code', 'avatar', 'jabatan_id', 'fingerprint_template'
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
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id', 'id');
    }

    // Mutator untuk menyimpan fingerprint template
    public function setFingerprintTemplateAttribute($value)
    {
        $this->attributes['fingerprint_template'] = base64_encode($value);
    }

    // Accessor untuk mengambil fingerprint template
    public function getFingerprintTemplateAttribute($value)
    {
        return base64_decode($value);
    }
}
