<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiKantor extends Model
{
    use HasFactory;

    protected $table = 'lokasi_kantor';

    protected $fillable = [
        'nama_kantor',
        'alamat',
        'latitude',
        'longitude',
    ];
}
