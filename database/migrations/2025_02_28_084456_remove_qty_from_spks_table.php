<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveQtyFromSpksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('spks', function (Blueprint $table) {
            if (Schema::hasColumn('spks', 'qty')) {
                $table->dropColumn('qty');
            }
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
            $table->integer('qty')->nullable();
        });
    }
}
