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
    Schema::create('revolut_tokens', function (Blueprint $table) {
    $table->id();
    $table->string('mode')->default('sandbox');
    $table->text('access_token');
    $table->text('refresh_token');
    $table->timestamp('access_token_expires_at')->nullable();
    $table->timestamp('refresh_token_expires_at')->nullable();
    $table->timestamps();

    $table->unique('mode');
});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('revolut_tokens');
    }
};
