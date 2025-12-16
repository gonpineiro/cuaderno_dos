<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('responsable_id')->nullable();

            // Relación polimórfica
            $table->morphs('ticketable');
            // genera: ticketable_id (bigint), ticketable_type (string)

            // Información del ticket
            $table->string('titulo');
            $table->text('descripcion')->nullable();


            $table->unsignedBigInteger('estado_id');
            $table->unsignedBigInteger('prioridad_id');

            $table->timestamps();

            $table->foreign('responsable_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('estado_id')->references('id')->on('tables')->onDelete('cascade');
            $table->foreign('prioridad_id')->references('id')->on('tables')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};
