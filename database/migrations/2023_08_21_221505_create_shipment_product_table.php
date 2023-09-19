<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipmentProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shipment_id');

            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->unsignedBigInteger('state_id');

            $table->integer('amount');
            $table->float('unit_price');

            $table->softDeletes();

            /* Relaciones */
            $table->foreign('shipment_id')->references('id')->on('price_quotes')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('state_id')->references('id')->on('tables')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipment_product');
    }
}
