<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientChasisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_chasis', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('client_id');
            $table->string('chasis')->nullable();
            $table->unsignedBigInteger('vehiculo_id')->nullable();
            $table->string('year')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('vehiculo_id')->references('id')->on('vehiculos');
            $table->foreign('client_id')->references('id')->on('clients');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_chasis');
    }
}
