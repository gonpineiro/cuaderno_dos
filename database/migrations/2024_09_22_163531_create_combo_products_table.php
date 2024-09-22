<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComboProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('combo_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('combo_id');

            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->integer('amount');
            $table->float('unit_price');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('combo_id')->references('id')->on('combos')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('combo_products');
    }
}
