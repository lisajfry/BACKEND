<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKaryawansTable extends Migration
{
    public function up()
    {
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_karyawan');
            $table->string('nik')->unique();
            $table->string('email')->unique();
            $table->string('no_handphone');
            $table->text('alamat');
            $table->string('password');
            $table->string('device_code')->nullable()->unique(); // Unique device code for login restriction
            $table->string('avatar')->nullable(); // Nullable avatar field
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('karyawans');
    }
}
