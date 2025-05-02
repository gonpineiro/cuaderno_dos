<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductJazzTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_jazz', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idProducto');
            $table->string('nombre')->nullable();
            $table->integer('stock');
            $table->float('precio_lista_2');
            $table->float('precio_lista_3');
            $table->float('precio_lista_6');
            $table->dateTime('fecha_alta');
            $table->dateTime('fecha_mod');
            $table->timestamps();

            $table->foreign('idProducto')->references('idProducto')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_jazz');
    }
}
