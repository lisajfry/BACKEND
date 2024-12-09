<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'kasbon_id',
        'tanggal_pembayaran',
        'jumlah_dibayar',
    ];

    public function kasbon()
    {
        return $this->belongsTo(Kasbon::class, 'kasbon_id'); // Relasi ke tabel kasbon
    }
}
