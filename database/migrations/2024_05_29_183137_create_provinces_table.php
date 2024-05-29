<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateProvincesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        DB::table('provinces')->insert(['name' => 'Ciudad Autónoma de Buenos Aires (CABA)']);
        DB::table('provinces')->insert(['name' => 'Buenos Aires']);
        DB::table('provinces')->insert(['name' => 'Catamarca']);
        DB::table('provinces')->insert(['name' => 'Córdoba']);
        DB::table('provinces')->insert(['name' => 'Corrientes']);
        DB::table('provinces')->insert(['name' => 'Entre Ríos']);
        DB::table('provinces')->insert(['name' => 'Jujuy']);
        DB::table('provinces')->insert(['name' => 'Mendoza']);
        DB::table('provinces')->insert(['name' => 'La Rioja']);
        DB::table('provinces')->insert(['name' => 'Salta']);
        DB::table('provinces')->insert(['name' => 'San Juan']);
        DB::table('provinces')->insert(['name' => 'San Luis']);
        DB::table('provinces')->insert(['name' => 'Santa Fe']);
        DB::table('provinces')->insert(['name' => 'Santiago del Estero']);
        DB::table('provinces')->insert(['name' => 'Tucumán']);
        DB::table('provinces')->insert(['name' => 'Chaco']);
        DB::table('provinces')->insert(['name' => 'Chubut']);
        DB::table('provinces')->insert(['name' => 'Formosa']);
        DB::table('provinces')->insert(['name' => 'Misiones']);
        DB::table('provinces')->insert(['name' => 'Neuquén']);
        DB::table('provinces')->insert(['name' => 'La Pampa']);
        DB::table('provinces')->insert(['name' => 'Río Negro']);
        DB::table('provinces')->insert(['name' => 'Santa Cruz']);
        DB::table('provinces')->insert(['name' => 'Tierra del Fuego']);

        Schema::table('cities', function (Blueprint $table) {

            // Si no eliminaste la columna, simplemente redefínela
            /* $table->unsignedBigInteger('province_id')->change(); */

            // Agregar la nueva relación con la tabla `provinces`
            $table->foreign('province_id')->references('id')->on('provinces')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('provinces');
    }
}
