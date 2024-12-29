<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->time('jam_keluar')->nullable()->after('longitude_masuk'); // Waktu keluar
            $table->longText('foto_keluar')->nullable()->after('jam_keluar'); // Foto keluar
            $table->decimal('latitude_keluar', 10, 7)->nullable()->after('foto_keluar'); // Latitude keluar
            $table->decimal('longitude_keluar', 10, 7)->nullable()->after('latitude_keluar'); // Longitude keluar
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->dropColumn(['jam_keluar', 'foto_keluar', 'latitude_keluar', 'longitude_keluar']);
        });
    }
};
