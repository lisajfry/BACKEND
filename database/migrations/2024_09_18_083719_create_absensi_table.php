<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('id_user')->constrained('users'); // Foreign key ke tabel users
            $table->date('tanggal'); // Tanggal absensi
            $table->time('jam_masuk')->nullable(); // Waktu masuk
            $table->time(column: 'jam_keluar')->nullable(); // Waktu keluar
            $table->string('foto_masuk')->nullable(); // Path file foto masuk
            $table->string('foto_keluar')->nullable(); // Path file foto keluar
            $table->string('lokasi_masuk')->nullable(); // Lokasi GPS saat masuk
            $table->string('lokasi_keluar')->nullable(); // Lokasi GPS saat keluar
            $table->timestamps(); // Created at dan Updated at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
