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
        Schema::create('master_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama lokasi (HQ, Branch, dll)
            $table->string('code')->unique(); // Kode lokasi (P001, dll)
            $table->string('company'); // PT/Company (PIS, dll)
            $table->text('address')->nullable(); // Alamat lengkap
            $table->string('phone')->nullable(); // Nomor telepon
            $table->string('email')->nullable(); // Email lokasi
            $table->boolean('is_active')->default(true); // Status aktif
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            
            $table->index(['is_active']);
            $table->index(['name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_locations');
    }
};
