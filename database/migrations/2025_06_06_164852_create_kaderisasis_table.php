<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kaderisasi', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 20); // FK ke users.nik, 1 user bisa input banyak kaderisasi
            $table->string('judul');
            $table->date('tanggal');
            $table->json('dokumentasi')->nullable(); // multiple files disimpan sebagai json
            $table->text('peserta')->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['terkirim', 'diterima', 'ditolak'])->default('terkirim');
            $table->text('alasan_tolak')->nullable();
            $table->timestamps();

            $table->foreign('nik')->references('nik')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kaderisasi');
    }
};
