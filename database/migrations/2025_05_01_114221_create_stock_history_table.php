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
        Schema::create('stock_history', function (Blueprint $table) {
            // Primary key
            $table->id(); // This will create an auto-incrementing 'id' column

            // Other columns
            $table->integer('product_id')->nullable();
            $table->string('product_type', 20)->nullable();
            $table->string('sku', 50)->nullable();
            $table->string('ean', 50)->nullable();
            $table->string('supplier', 50)->nullable();
            $table->string('qty', 20)->nullable();
            $table->decimal('cost_price', 10, 2)->default(0.00);
            $table->string('stock_type', 20)->nullable();
            $table->string('reason', 100)->nullable();
            $table->string('other_reason', 100)->nullable();
            $table->date('stock_date')->nullable();
            $table->integer('ref_id')->nullable();
            $table->string('ref_type', 50)->nullable();

            // Timestamps
            $table->timestamps(); // Adds 'created_at' and 'updated_at' columns
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_history');
    }
};
