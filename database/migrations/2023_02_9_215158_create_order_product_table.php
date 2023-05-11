<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');

            /* Requieren que sea nulos porque o va ser uno o el otro, jamas ambos */
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('other_id')->nullable();
            $table->unsignedBigInteger('state_id');

            /* Detalle del pedido */
            $table->integer('amount');
            $table->float('unit_price');
            $table->string('description')->nullable();

            $table->softDeletes();

            /* Relaciones */
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            /* $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('other_id')->references('id')->on('product_others')->onDelete('cascade'); */
            $table->foreign('state_id')->references('id')->on('tables')->onDelete('cascade');

            /* $table->unique(['order_id', 'product_id']);
            $table->unique(['order_id', 'other_id']); */
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_product');
    }
}
