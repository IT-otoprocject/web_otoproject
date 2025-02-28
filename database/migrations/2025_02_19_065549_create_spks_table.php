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
            $table->date('tanggal');
            $table->string('teknisi_1');
            $table->string('teknisi_2')->nullable();
            $table->string('customer');
            $table->string('alamat');
            $table->string('no_hp');
            $table->string('jenis_mobil');
            $table->string('no_plat');
            $table->text('catatan')->nullable();
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
