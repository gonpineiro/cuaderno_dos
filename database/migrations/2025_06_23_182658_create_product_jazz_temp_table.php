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
        Schema::create('product_jazz_temp', function (Blueprint $table) {
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
            $table->string('state')->nullable();
            $table->dateTime('fecha_alta');
            $table->dateTime('fecha_mod');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_jazz_temp');
    }
}
