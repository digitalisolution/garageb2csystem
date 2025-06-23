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
        // Add workshop_origin column to the workshop table
        Schema::table('workshops', function (Blueprint $table) {
            $table->string('workshop_origin')->nullable()->after('status'); // Adjust position if needed
        });

        // Add workshop_origin column to the invoice table
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('workshop_origin')->nullable()->after('status'); // Adjust position if needed
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the workshop_origin column from the workshop table
        Schema::table('workshops', function (Blueprint $table) {
            $table->dropColumn('workshop_origin');
        });

        // Drop the workshop_origin column from the invoice table
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('workshop_origin');
        });
    }
};
