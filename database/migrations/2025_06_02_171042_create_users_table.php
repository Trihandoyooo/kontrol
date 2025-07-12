<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('nik', 20)->primary();
            $table->string('name'); // nama lengkap
            $table->string('gelar_depan')->nullable();
            $table->string('gelar_belakang')->nullable();
            $table->string('email')->unique();
            $table->string('password');

            // Data pribadi
            $table->string('foto_ktp')->nullable();
            $table->string('nomor_kta')->nullable();
            $table->string('foto_kta')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->integer('usia')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('agama')->nullable();
            $table->string('status_perkawinan')->nullable();

            // Alamat KTP
            $table->text('alamat_ktp')->nullable();
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kelurahan_desa')->nullable();
            $table->string('kabupaten')->default('Bengkalis');

            // Data Khusus untuk Role: user
            $table->string('foto_profil')->nullable();
            $table->string('dapil')->nullable();
            $table->integer('jumlah_suara')->default(0);
            $table->integer('jumlah_suara_sebelumnya')->default(0);
            $table->integer('jumlah_tim')->default(0);

            $table->enum('role', ['admin', 'ketua', 'user'])->default('user');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
