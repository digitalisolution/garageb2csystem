<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Insert data into the general_settings table
        DB::table('general_settings')->insert([
           
            [
                'id' => 89,
                 'name' => 'calendar_booking_color_canceled',
                'value' => '#4400ff',
                'status' => 1,
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'id' => 90,
                 'name' => 'calendar_booking_color_failed',
                'value' => '#4400ff',
                'status' => 1,
                'created_at' => null,
                'updated_at' => null,
            ],
            [
                'id' => 91,
                 'name' => 'calendar_booking_color_awaiting',
                'value' => '#4400ff',
                'status' => 1,
                'created_at' => null,
                'updated_at' => null,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove the inserted rows when rolling back the migration
        DB::table('general_settings')->whereIn('id', [84, 85, 86, 87, 88])->delete();
    }
};
