<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('provider_code')->nullable();
            $table->string('factory_code')->nullable();
            $table->string('equivalence')->nullable();

            $table->string('description')->nullable();
            $table->string('model')->nullable();
            $table->string('engine')->nullable();
            $table->string('observation')->nullable();

            $table->boolean('min_stock')->nullable();
            $table->boolean('empty_stock')->nullable();

            $table->string('ship')->nullable();
            $table->string('module')->nullable();
            $table->string('side')->nullable();
            $table->string('column')->nullable();
            $table->string('row')->nullable();

            $table->boolean('verified')->default(0);

            $table->unsignedBigInteger('provider_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();

            $table->timestamps();

            /* Relaciones */
            $table->foreign('brand_id')->references('id')->on('tables');
            $table->foreign('provider_id')->references('id')->on('providers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
