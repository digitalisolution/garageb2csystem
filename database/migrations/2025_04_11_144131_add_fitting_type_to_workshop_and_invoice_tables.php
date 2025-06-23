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
        // Add fitting_type column to the workshop table
        Schema::table('workshops', function (Blueprint $table) {
            $table->string('fitting_type')->nullable()->after('status'); // Adjust position if needed
        });

        // Add fitting_type column to the invoice table
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('fitting_type')->nullable()->after('status'); // Adjust position if needed
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the fitting_type column from the workshop table
        Schema::table('workshops', function (Blueprint $table) {
            $table->dropColumn('fitting_type');
        });

        // Drop the fitting_type column from the invoice table
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('fitting_type');
        });
    }
};
