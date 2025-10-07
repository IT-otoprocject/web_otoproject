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
        Schema::table('purchase_requests', function (Blueprint $table) {
            // FAT approval department selection (PIS, MMI, AOS, etc)
            $table->string('fat_department')->nullable()->after('approvals');
            
            // FAT approval type (asset or cost)
            $table->enum('fat_approval_type', ['asset', 'cost'])->nullable()->after('fat_department');
            
            // Asset number field (filled by GA after purchasing complete, only if fat_approval_type = 'asset')
            $table->string('asset_number')->nullable()->after('fat_approval_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropColumn(['fat_department', 'fat_approval_type', 'asset_number']);
        });
    }
};
