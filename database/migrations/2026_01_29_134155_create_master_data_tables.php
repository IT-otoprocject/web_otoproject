<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabel Master Divisi
        Schema::create('master_divisi', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique(); // Kode divisi (FACTORY, FAT, dll)
            $table->string('nama'); // Nama lengkap divisi
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabel Master User Level
        Schema::create('master_user_level', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique(); // Kode level (admin, manager, dll)
            $table->string('nama'); // Nama lengkap level
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabel Master Garage
        Schema::create('master_garage', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique(); // Kode garage
            $table->string('nama'); // Nama garage
            $table->string('lokasi')->nullable(); // Lokasi garage
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert data default dari ENUM yang sudah ada
        // Master Divisi
        $divisiData = [
            ['kode' => 'FACTORY', 'nama' => 'Factory', 'is_active' => true],
            ['kode' => 'FAT', 'nama' => 'FAT', 'is_active' => true],
            ['kode' => 'HCGA', 'nama' => 'HC & GA', 'is_active' => true],
            ['kode' => 'RETAIL', 'nama' => 'Retail', 'is_active' => true],
            ['kode' => 'PDCA', 'nama' => 'PDCA', 'is_active' => true],
            ['kode' => 'PURCHASING', 'nama' => 'Purchasing', 'is_active' => true],
            ['kode' => 'R&D', 'nama' => 'Research & Development', 'is_active' => true],
            ['kode' => 'SALES', 'nama' => 'Sales', 'is_active' => true],
            ['kode' => 'WAREHOUSE', 'nama' => 'Warehouse', 'is_active' => true],
            ['kode' => 'WAREHOUSE_SBY', 'nama' => 'Warehouse Surabaya', 'is_active' => true],
        ];

        foreach ($divisiData as $divisi) {
            DB::table('master_divisi')->insert(array_merge($divisi, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Master User Level
        $levelData = [
            ['kode' => 'admin', 'nama' => 'Administrator', 'deskripsi' => 'Full system access', 'is_active' => true],
            ['kode' => 'manager', 'nama' => 'Manager', 'deskripsi' => 'Manager level access', 'is_active' => true],
            ['kode' => 'spv', 'nama' => 'Supervisor', 'deskripsi' => 'Supervisor level access', 'is_active' => true],
            ['kode' => 'staff', 'nama' => 'Staff', 'deskripsi' => 'Staff level access', 'is_active' => true],
            ['kode' => 'headstore', 'nama' => 'Head Store', 'deskripsi' => 'Head store level access', 'is_active' => true],
            ['kode' => 'kasir', 'nama' => 'Kasir', 'deskripsi' => 'Cashier level access', 'is_active' => true],
            ['kode' => 'sales', 'nama' => 'Sales', 'deskripsi' => 'Sales level access', 'is_active' => true],
            ['kode' => 'mekanik', 'nama' => 'Mekanik', 'deskripsi' => 'Mechanic level access', 'is_active' => true],
            ['kode' => 'ceo', 'nama' => 'CEO', 'deskripsi' => 'Chief Executive Officer', 'is_active' => true],
            ['kode' => 'cfo', 'nama' => 'CFO', 'deskripsi' => 'Chief Financial Officer', 'is_active' => true],
        ];

        foreach ($levelData as $level) {
            DB::table('master_user_level')->insert(array_merge($level, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Master Garage - ambil data unik dari tabel users yang sudah ada
        $existingGarages = DB::table('users')
            ->whereNotNull('garage')
            ->where('garage', '!=', '')
            ->distinct()
            ->pluck('garage');

        foreach ($existingGarages as $garage) {
            DB::table('master_garage')->insert([
                'kode' => strtoupper(str_replace(' ', '_', $garage)),
                'nama' => $garage,
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
        Schema::dropIfExists('master_garage');
        Schema::dropIfExists('master_user_level');
        Schema::dropIfExists('master_divisi');
    }
};
