<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductBrandTableAndAlterProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('product_brand_id')->nullable()->after('brand_id');
            $table->string('rubro')->nullable()->after('product_brand_id');
            $table->string('subrubro')->nullable()->after('rubro');

            $table->foreign('product_brand_id')->references('id')->on('product_brands');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['product_brand_id']);
            $table->dropColumn('product_brand_id');
            $table->dropColumn('rubro');
            $table->dropColumn('subrubro');
        });
        Schema::dropIfExists('product_brands');
    }
}
