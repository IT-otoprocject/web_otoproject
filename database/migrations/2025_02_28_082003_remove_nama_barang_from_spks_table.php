<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveNamaBarangAndQtyFromSpksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('spks', function (Blueprint $table) {
            $table->dropColumn('nama_barang');
            $table->dropColumn('qty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('spks', function (Blueprint $table) {
            $table->string('nama_barang')->nullable();
            $table->integer('qty')->nullable();
        });
    }
}
