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
        Schema::create('pr_number_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('prefix', 10); // PO, etc
            $table->date('date'); // tanggal untuk sequence
            $table->integer('last_sequence')->default(0); // sequence terakhir
            $table->timestamps();
            
            // Unique constraint untuk prefix + date
            $table->unique(['prefix', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pr_number_sequences');
    }
};
