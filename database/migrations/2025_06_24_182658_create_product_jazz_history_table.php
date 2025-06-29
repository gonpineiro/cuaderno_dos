<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductJazzTempTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_jazz_history', callback: function (Blueprint $table) {
            $table->unsignedBigInteger('sinc_id')->primary();
            $table->unsignedBigInteger('id')->primary();
            $table->string('nombre')->nullable();
            $table->string('code')->nullable();
            $table->string('provider_code')->nullable();
            $table->string('equivalence')->nullable();
            $table->string('observation')->nullable();
            $table->string('ubicacion')->nullable();
            $table->integer('stock');
            $table->double('precio_lista_2', 12, 2);
            $table->double('precio_lista_3', 12, 2);
            $table->double('precio_lista_6', 12, 2);
            $table->dateTime('fecha_alta');
            $table->dateTime('fecha_mod');

            $table->unique(['sinc_id', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_jazz_history');
    }
}
