<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('customer_vehicle')) {
            Schema::create('customer_vehicle', function (Blueprint $table) {
                $table->integer('id', true); // Primary key with auto-increment (INT 11)
                $table->integer('customer_id')->index(); // INT(11) without unsigned
                $table->integer('vehicle_detail_id')->index(); // INT(11) without unsigned
                $table->timestamps();
    
                // Manually adding foreign key constraints
                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
                $table->foreign('vehicle_detail_id')->references('id')->on('vehicle_details')->onDelete('cascade');
            });
        } else {
            \Log::info("Table 'customer_vehicle' already exists. Skipping creation.");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_vehicle');
    }
};
