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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nama_karyawan');
            $table->string('nik')->unique();
            $table->string('no_handphone');
            $table->text('alamat');
        });
    }
    
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nama_karyawan', 'nik', 'no_handphone', 'alamat']);
        });
    }
    
};
