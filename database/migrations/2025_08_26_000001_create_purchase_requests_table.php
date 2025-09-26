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
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->string('pr_number')->unique(); // Format: PO/DDMMYY1 (sequence resets daily)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('request_date');
            $table->date('due_date')->nullable(); // Jatuh tempo untuk PR pembayaran
            $table->text('description'); // Keterangan kebutuhan
            $table->enum('location', ['HQ', 'BRANCH', 'OTHER'])->default('HQ');
            $table->enum('status', ['DRAFT', 'SUBMITTED', 'APPROVED', 'REJECTED', 'COMPLETED'])->default('DRAFT');
            $table->json('approval_flow'); // JSON untuk menyimpan approval flow yang dipilih user
            $table->json('approvals')->nullable(); // JSON untuk menyimpan status approval
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};
