<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEoriNumberToGarageDetailTable extends Migration
{
    public function up()
    {
        Schema::table('garage_details', function (Blueprint $table) {
            if (!Schema::hasColumn('garage_details', 'eori_number')) {
                $table->string('eori_number', 20)->nullable()->after('vat_number');
            }
        });
    }

    public function down()
    {
        Schema::table('garage_details', function (Blueprint $table) {
            if (Schema::hasColumn('garage_details', 'eori_number')) {
                $table->dropColumn('eori_number');
            }
        });
    }
}