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
    public function up(): void
    {
        Schema::table('customer_debit_logs', function (Blueprint $table) {
            // Add a new column 'payment_history_id'
            $table->Integer('payment_history_id')->nullable()->after('workshop_id');

            // Add a foreign key constraint
            $table->foreign('payment_history_id')
                  ->references('id')
                  ->on('payment_histories')
                  ->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('customer_debit_logs', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['payment_history_id']);

            // Drop the column
            $table->dropColumn('payment_history_id');
        });
    }
};
