<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_history', function (Blueprint $table) {
            $table->integer('available_qty')->nullable()->after('qty'); // or after any relevant column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_history', function (Blueprint $table) {
            $table->dropColumn('available_qty');
        });
    }
};
