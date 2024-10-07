<?php

namespace App\Http\Middleware;

// Menggunakan middleware autentikasi bawaan Laravel
use Illuminate\Auth\Middleware\Authenticate as Middleware;
// Menggunakan class Request untuk menangani permintaan HTTP
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the karyawan should be redirected to when they are not authenticated.
     */
    // Mengecek apakah request mengharapkan respon JSON
    // Jika tidak mengharapkan JSON, pengguna akan diarahkan ke halaman login
     protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }

    
}
