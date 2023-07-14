<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('price_quote_id')->nullable();

            $table->string('engine');
            $table->string('chasis')->nullable();

            $table->string('payment_method')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('remito')->nullable();
            $table->string('workshop')->nullable();
            $table->float('deposit')->default(0);
            $table->date('estimated_date')->nullable();
            $table->string('observation')->nullable();

            $table->timestamps();
            $table->softDeletes();

            /* Relaciones */
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('type_id')->references('id')->on('tables')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
