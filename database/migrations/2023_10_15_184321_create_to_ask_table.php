<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToAskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('to_ask', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_product_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->boolean('purchase_order')->default(true);

            $table->integer('amount');

            /* Relaciones */
            $table->foreign('order_product_id')->references('id')->on('order_product')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('to_ask');
    }
}
