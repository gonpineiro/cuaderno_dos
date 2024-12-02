<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVehiculoYearChasisCaontactoInShipments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('year')->nullable()->after('order_id');
            $table->unsignedBigInteger('vehiculo_id')->nullable()->after('year');
            $table->string('chasis')->nullable()->after('vehiculo_id');
            $table->string('contacto')->nullable()->after('chasis');

            $table->foreign('vehiculo_id')->references('id')->on('vehiculos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipments', function (Blueprint $table) {
            //
        });
    }
}
