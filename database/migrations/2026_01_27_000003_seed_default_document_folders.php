<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $folders = [
            ['name' => 'SOP', 'slug' => 'sop', 'description' => 'Standard Operating Procedure', 'order' => 1],
            ['name' => 'WIP', 'slug' => 'wip', 'description' => 'Work Instruction Procedure', 'order' => 2],
            ['name' => 'Form', 'slug' => 'form', 'description' => 'Form-form perusahaan', 'order' => 3],
            ['name' => 'PICA', 'slug' => 'pica', 'description' => 'Problem Identification and Corrective Action', 'order' => 4],
            ['name' => 'SKD', 'slug' => 'skd', 'description' => 'Surat Keputusan Direksi', 'order' => 5],
            ['name' => 'Internal Memo', 'slug' => 'internal-memo', 'description' => 'Internal Memorandum', 'order' => 6],
        ];

        foreach ($folders as $folder) {
            DB::table('document_folders')->insert([
                'name' => $folder['name'],
                'slug' => $folder['slug'],
                'description' => $folder['description'],
                'icon' => 'folder',
                'order' => $folder['order'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('document_folders')->whereIn('slug', [
            'sop', 'wip', 'form', 'pica', 'skd', 'internal-memo'
        ])->delete();
    }
};
