<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('engine');

            $table->integer('year')->nullable()->after('shipment_id');
            $table->unsignedBigInteger('vehiculo_id')->nullable()->after('year');

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
        Schema::table('orders', function (Blueprint $table) {
            // Restaurar la columna 'engine'
            $table->dropForeign(['vehiculo_id']);
            $table->string('engine');

            // Eliminar las columnas agregadas
            $table->dropColumn('year');
            $table->dropColumn('vehiculo_id');
            $table->dropColumn('contacto');
        });
    }
}
