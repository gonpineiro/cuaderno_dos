<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePriceQuoteProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_quote_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('price_quote_id');

            /* Requieren que sea nulos porque o va ser uno o el otro, jamas ambos */
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('other_id')->nullable();
            $table->unsignedBigInteger('state_id');

            /* Detalle del pedido */
            $table->integer('amount');
            $table->float('unit_price');
            $table->boolean('quote')->default(1);
            $table->string('description')->nullable();

            $table->softDeletes();

            /* Relaciones */
            $table->foreign('price_quote_id')->references('id')->on('price_quotes')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('other_id')->references('id')->on('product_others')->onDelete('cascade');
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
        Schema::dropIfExists('price_quote_product');
    }
}
