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
    Schema::table('iurans', function (Blueprint $table) {
        $table->text('alasan_tolak')->nullable();
    });
}

public function down()
{
    Schema::table('iurans', function (Blueprint $table) {
        $table->dropColumn('alasan_tolak');
    });
}

};
