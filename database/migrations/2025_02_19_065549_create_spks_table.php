<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spks', function (Blueprint $table) {
            $table->id();
            $table->string('no_spk')->unique();
            $table->string('garage');
            $table->date('tanggal');
            $table->string('customer');
            $table->string('alamat');
            $table->string('no_hp');
            $table->string('jenis_mobil');
            $table->string('no_plat');
            $table->text('catatan')->nullable();
            $table->string('status')->default('Baru Diterbitkan');
            $table->string('waktu_kerja')->nullable();
            $table->text('catatan_kerja')->nullable();
            $table->string('teknisi_selesai')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->timestamp('waktu_terbit_spk')->nullable();
            $table->timestamp('waktu_mulai_kerja')->nullable();
            $table->text('durasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spks');
    }
}
