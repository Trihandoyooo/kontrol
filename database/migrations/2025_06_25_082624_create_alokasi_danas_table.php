<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('alokasi_danas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kegiatan');            // Nama kegiatan, contoh: "Sosialisasi"
            $table->decimal('jumlah', 15, 2);           // Jumlah dalam rupiah
            $table->date('tanggal');                    // Tanggal alokasi dana
            $table->text('deskripsi')->nullable();      // Penjelasan tambahan
            $table->text('dokumentasi')->nullable();    // Dokumentasi berupa JSON (multi-file)
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('alokasi_danas');
    }
};
