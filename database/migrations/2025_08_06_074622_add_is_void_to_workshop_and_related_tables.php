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
         Schema::table('workshops', function (Blueprint $table) {
            $table->boolean('is_void')->default(false)->after('id'); // Change 'id' to any column you prefer
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->boolean('is_void')->default(false)->after('workshop_id'); // Or after('customer_id'), etc.
        });

        Schema::table('workshop_tyres', function (Blueprint $table) {
            $table->boolean('is_void')->default(false)->after('workshop_id');
        });

        Schema::table('workshop_services', function (Blueprint $table) {
            $table->boolean('is_void')->default(false)->after('workshop_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workshops', function (Blueprint $table) {
            $table->dropColumn('is_void');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('is_void');
        });

        Schema::table('workshop_tyres', function (Blueprint $table) {
            $table->dropColumn('is_void');
        });

        Schema::table('workshop_services', function (Blueprint $table) {
            $table->dropColumn('is_void');
        });
    }
};
