<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddYearVehiculoIdColumnInClient extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('year')->nullable()->after('reference_id');
            $table->unsignedBigInteger('vehiculo_id')->nullable()->after('year');

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
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['vehiculo_id']);
            $table->dropColumn('year');
            $table->dropColumn('vehiculo_id');
        });
    }
}
