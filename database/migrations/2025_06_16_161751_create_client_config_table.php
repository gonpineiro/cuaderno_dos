<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_config', function (Blueprint $table) {
            $table->id();

            $table->string('type'); //cotzaciones

            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('information_source_id');
            $table->unsignedBigInteger('type_price_id');
            $table->boolean('es_cuenta_corriente')->nullable();
            $table->timestamps();

            /* Relaciones */
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('information_source_id')->references('id')->on('tables');
            $table->foreign('type_price_id')->references('id')->on('tables');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_config');
    }
}
