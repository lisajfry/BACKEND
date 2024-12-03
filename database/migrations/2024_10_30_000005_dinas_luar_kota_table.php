<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dinas_luar_kota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_karyawan')->constrained('karyawans')->onDelete('cascade');
            $table->date('tgl_berangkat');
            $table->date('tgl_kembali');
            $table->string('kota_tujuan');
            $table->string('keperluan');
            $table->decimal('biaya_transport', 10, 2);
            $table->decimal('biaya_penginapan', 10, 2);
            $table->decimal('uang_harian', 10, 2);
            $table->decimal('total_biaya', 10, 2)->nullable(); // Ini akan dihitung otomatis
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dinas_luar_kota');
    }
};