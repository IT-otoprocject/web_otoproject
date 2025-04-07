<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsNewToSpkItemsTable extends Migration
{
    public function up()
    {
        Schema::table('spk_items', function (Blueprint $table) {
            $table->boolean('is_new')->default(false)->after('qty');
        });
    }

    public function down()
    {
        Schema::table('spk_items', function (Blueprint $table) {
            $table->dropColumn('is_new');
        });
    }
}
