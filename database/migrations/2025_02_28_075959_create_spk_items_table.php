<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpkItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spk_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spk_id')->constrained('spks')->onDelete('cascade'); // Menghubungkan dengan tabel spks
            $table->string('nama_barang');
            $table->integer('qty');
            $table->boolean('is_new')->default(false);
            $table->unsignedBigInteger('mekanik_id')->nullable(); // Tambah kolom mekanik_id
            $table->string('waktu_pengerjaan_barang', 8)->nullable(); // Kolom baru untuk durasi HH:mm:ss
            $table->string('sku')->nullable(); // Kolom baru untuk SKU produk
            $table->string('waktu_sebelumnya', 8)->nullable();
            $table->string('selisih_waktu', 8)->nullable();
            // Jika ingin relasi ke tabel users:
            // $table->foreign('mekanik_id')->references('id')->on('users')->nullOnDelete();
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
        Schema::dropIfExists('spk_items');
    }
}
