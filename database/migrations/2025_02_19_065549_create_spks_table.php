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
    Schema::create('spks', function (Blueprint $table) {
        $table->id();
        $table->string('no_spk')->unique();
        $table->date('tanggal');
        $table->string('no_so');
        $table->string('teknisi_1');
        $table->string('teknisi_2')->nullable();
        $table->string('customer');
        $table->text('alamat');
        $table->string('no_hp');
        $table->string('jenis_mobil');
        $table->string('no_plat');
        $table->string('nama_barang');
        $table->integer('qty');
        $table->text('catatan')->nullable();
        $table->string('status')->default('baru diterbitkan');
        $table->timestamps();
    });
}
    // public function up(): void
    // {
    //     Schema::create('spks', function (Blueprint $table) {
    //         $table->id();
    //         $table->timestamps();
    //     });
    // }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spks');
    }
};
