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
        // Add 'ref_type' to 'workshop_tyres'
        Schema::table('workshop_tyres', function (Blueprint $table) {
            $table->string('ref_type')->nullable()->after('workshop_id');
        });

        // Add 'ref_type' to 'workshop_services'
        Schema::table('workshop_services', function (Blueprint $table) {
            $table->string('ref_type')->nullable()->after('workshop_id');
        });

        // Rename column in 'estimates'
        Schema::table('estimates', function (Blueprint $table) {
            $table->renameColumn('is_converted_to_invoice', 'is_converted_to_workshop');
        });
    }

    public function down()
    {
        // Remove 'ref_type' from 'workshop_tyres'
        Schema::table('workshop_tyres', function (Blueprint $table) {
            $table->dropColumn('ref_type');
        });

        // Remove 'ref_type' from 'workshop_services'
        Schema::table('workshop_services', function (Blueprint $table) {
            $table->dropColumn('ref_type');
        });

        // Revert column rename in 'estimates'
        Schema::table('estimates', function (Blueprint $table) {
            $table->renameColumn('is_converted_to_workshop', 'is_converted_to_invoice');
        });
    }
};
