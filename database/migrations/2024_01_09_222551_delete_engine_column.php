<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteEngineColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('price_quotes', function (Blueprint $table) {
            $table->dropColumn('engine');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('engine');
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
            $table->string('engine')->after('shipment_id')->nullable();
        });

        Schema::table('price_quotes', function (Blueprint $table) {
            $table->string('engine')->after('year')->nullable();
        });
    }
}
