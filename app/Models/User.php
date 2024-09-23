<?php

namespace App\Models;
// Menggunakan class Authenticatable untuk fungsi autentikasi standar
use Illuminate\Foundation\Auth\User as Authenticatable;
// Trait untuk mengirimkan notifikasi (misalnya, email) kepada user
use Illuminate\Notifications\Notifiable;
// Mengimplementasikan JWTSubject untuk menggunakan JWT (JSON Web Token)
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    // Trait untuk mengaktifkan fitur notifikasi dalam model User
    use Notifiable;

    // Mendefinisikan kolom-kolom yang boleh diisi dalam tabel users
    protected $fillable = [
        'nama_karyawan', 'nik', 'email', 'no_handphone', 'alamat', 'password'
    ];

    // Menyembunyikan kolom-kolom yang tidak boleh diakses secara langsung dalam bentuk array atau JSON
    protected $hidden = [
        'password', 'remember_token',
    ];

    // Mengambil identifier utama dari JWT, dalam hal ini adalah primary key (ID) pengguna
    public function getJWTIdentifier( )
    {
        // Mengambil primary key dari user yang sedang login
        return $this->getKey();
    }

    // Menambahkan klaim kustom ke token JWT (jika ada)
    public function getJWTCustomClaims()
    {
        // Tidak ada klaim kustom dalam hal ini, hanya mengembalikan array kosong
        return [];
    }

        public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

}
