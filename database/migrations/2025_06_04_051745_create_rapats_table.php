<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rapats', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 20); // foreign key ke users.nik, jangan unique karena 1 user bisa input banyak rapat
            $table->string('jenis_rapat');
            $table->string('judul');
            $table->text('notulen')->nullable();
            $table->string('lokasi')->nullable();
            $table->date('tanggal');
            // Dokumentasi simpan file banyak, sebaiknya di tabel terpisah, tapi kalau mau simpan json di sini:
            $table->json('dokumentasi')->nullable();
            $table->enum('status', ['terkirim', 'diterima', 'ditolak'])->default('terkirim');
            $table->text('alasan_tolak')->nullable();
            $table->text('peserta')->nullable(); // tambah kolom peserta
            $table->timestamps();

            $table->foreign('nik')->references('nik')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rapats');
    }
};

