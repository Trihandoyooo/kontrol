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
        Schema::create('notifikasi', function (Blueprint $table) {
    $table->id();
    $table->string('nik');
    $table->foreign('nik')->references('nik')->on('users')->onDelete('cascade');
    $table->string('tipe'); // "rapat", "iuran", dll
    $table->text('pesan');
    $table->unsignedTinyInteger('peringatan_ke')->default(1); // 1, 2, 3
    $table->boolean('dibaca')->default(false);
    $table->timestamp('dibaca_pada')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
