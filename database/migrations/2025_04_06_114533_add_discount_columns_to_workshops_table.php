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
        // Add columns to the workshops table
        Schema::table('workshops', function (Blueprint $table) {
            $table->enum('discount_type', ['amount', 'percentage'])->nullable()->after('payment_method');
            $table->decimal('discount_value', 10, 2)->nullable()->after('discount_type');
        });
    
        // Add columns to the invoices table
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('discount_type', ['amount', 'percentage'])->nullable()->after('payment_method');
            $table->decimal('discount_value', 10, 2)->nullable()->after('discount_type');
        });
    }
    
    public function down()
    {
        // Drop columns from the workshops table
        Schema::table('workshops', function (Blueprint $table) {
            $table->dropColumn('discount_type');
            $table->dropColumn('discount_value');
        });
    
        // Drop columns from the invoices table
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('discount_type');
            $table->dropColumn('discount_value');
        });
    }
};
