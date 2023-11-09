<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePriceQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_quotes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('order_id')->nullable();

            $table->unsignedBigInteger('brand_id');
            $table->integer('year')->nullable();

            $table->string('engine');
            $table->string('chasis')->nullable();

            $table->unsignedBigInteger('information_source_id');
            $table->unsignedBigInteger('type_price_id');
            $table->string('observation')->nullable();

            $table->timestamps();
            $table->softDeletes();

            /* Relaciones */
            $table->foreign('brand_id')->references('id')->on('brands');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('information_source_id')->references('id')->on('tables');
            $table->foreign('type_price_id')->references('id')->on('tables');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('price_quote_id')->references('id')->on('price_quotes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['price_quote_id']);
        });

        Schema::dropIfExists('price_quotes');
    }
}
