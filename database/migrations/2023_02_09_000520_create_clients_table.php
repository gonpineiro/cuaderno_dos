<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('dni')->unique();
            $table->string('name', 35);
            $table->string('email', 100)->unique()->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('city')->nullable();
            $table->string('adress')->nullable();
            $table->string('cuit')->nullable();
            $table->boolean('is_company')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
