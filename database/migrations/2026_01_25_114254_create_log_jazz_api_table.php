<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogJazzApiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_jazz_api', function (Blueprint $table) {
            $table->id();
            $table->string('endpoint');
            $table->string('metod');
            $table->unsignedBigInteger('user_id');

            $table->json('request')->nullable();
            $table->json('response')->nullable();

            $table->json('error')->nullable();
            $table->integer('time_ms')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jaz_log_');
    }
}
