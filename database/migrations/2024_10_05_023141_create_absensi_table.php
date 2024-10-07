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
            $table->foreignId('id_karyawan')
                ->constrained('karyawans') // Mengacu pada tabel karyawans
                ->onDelete('cascade'); // Jika karyawan dihapus, data absensi juga ikut dihapus

            $table->date('tanggal'); // Tanggal absensi
            $table->time('jam_masuk')->nullable(); // Waktu masuk
            $table->time('jam_keluar')->nullable(); // Waktu keluar

            // Ubah kolom foto_masuk dan foto_keluar menjadi longText
            $table->longText('foto_masuk')->nullable(); // Path file foto masuk
            $table->longText('foto_keluar')->nullable(); // Path file foto keluar

            // Gunakan decimal untuk GPS coordinates (lat, lon)
            $table->decimal('latitude_masuk', 10, 7)->nullable(); // Latitude lokasi masuk
            $table->decimal('longitude_masuk', 10, 7)->nullable(); // Longitude lokasi masuk
            $table->decimal('latitude_keluar', 10, 7)->nullable(); // Latitude lokasi keluar
            $table->decimal('longitude_keluar', 10, 7)->nullable(); // Longitude lokasi keluar

            // Simpan lokasi hasil reverse geocoding
            $table->string('lokasi_masuk')->nullable(); // Lokasi saat masuk
            $table->string('lokasi_keluar')->nullable(); // Lokasi saat keluar

            // Tambahkan kolom status
            $table->enum('status', ['hadir', 'alfa', 'izin', 'sakit'])->default('hadir');

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
