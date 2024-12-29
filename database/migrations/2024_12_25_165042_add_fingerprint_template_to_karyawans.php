<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('karyawans', function (Blueprint $table) {
            // Menambahkan kolom untuk menyimpan template sidik jari
            $table->text('fingerprint_template')->nullable();  // Menyimpan template fingerprint dalam format string atau binary
        });
    }

    public function down()
    {
        Schema::table('karyawans', function (Blueprint $table) {
            // Menghapus kolom fingerprint_template saat rollback
            $table->dropColumn('fingerprint_template');
        });
    }
};
