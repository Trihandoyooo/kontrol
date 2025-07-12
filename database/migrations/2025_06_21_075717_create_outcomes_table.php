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
        Schema::create('outcomes', function (Blueprint $table) {
    $table->id();
    $table->string('nik'); // Gunakan nik sebagai foreign key
    $table->string('judul');
    $table->string('nama_kegiatan');
    $table->text('keterangan')->nullable();
    $table->string('dapil');
    $table->text('manfaat')->nullable();
    $table->json('dokumentasi')->nullable();
    $table->date('tanggal');
    $table->timestamps();

    $table->foreign('nik')->references('nik')->on('users')->onDelete('cascade');
});


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outcomes');
    }
};
