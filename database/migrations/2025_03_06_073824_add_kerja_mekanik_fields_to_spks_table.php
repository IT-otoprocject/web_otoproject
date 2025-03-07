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
        Schema::table('spks', function (Blueprint $table) {
            // $table->string('status')->default('Belum Selesai')->change();
            $table->string('waktu_kerja')->nullable();
            $table->text('catatan_kerja')->nullable();
            $table->string('teknisi_selesai')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('spks', function (Blueprint $table) {
            $table->dropColumn(['waktu_kerja', 'catatan_kerja', 'teknisi_selesai']);
        });
    }
};
