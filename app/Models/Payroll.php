<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    public function izin()
    {
        return $this->hasMany(Izin::class);
    }

    public function dinasLuar()
    {
        return $this->hasMany(DinasLuar::class);
    }

    public function karyawan()
{
    return $this->belongsTo(Karyawan::class);
}

}

