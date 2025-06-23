<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('workshops', function (Blueprint $table) {
            $table->string('country')->nullable()->after('county'); // Adjust column position if needed
        });
    }

    public function down()
    {
        Schema::table('workshops', function (Blueprint $table) {
            $table->dropColumn('country');
        });
    }
};
