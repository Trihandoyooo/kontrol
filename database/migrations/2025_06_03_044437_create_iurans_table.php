<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('iurans', function (Blueprint $table) {
            $table->id();
            $table->string('nik');
            $table->foreign('nik')->references('nik')->on('users')->onDelete('cascade');
            $table->string('jenis_iuran'); // 7 kategori tetap
            $table->decimal('nominal', 15, 2);
            $table->date('tanggal');
            $table->text('catatan')->nullable();
            $table->string('dokumentasi')->nullable();
            
            // Kolom tambahan
            $table->string('status')->default('terkirim'); // terkirim, diterima, tidak diterima
            $table->string('alasan_ditolak')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('iurans');
    }
};
