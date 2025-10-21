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
            $table->unsignedBigInteger('shipment_id')->nullable();

            $table->string('engine')->nullable();
            $table->string('chasis')->nullable();
            $table->string('observation')->nullable();

            /** Pedido Online */
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->string('invoice_number')->nullable();

            /* Pedidos cliente */
            $table->float('deposit')->nullable();
            $table->date('estimated_date')->nullable();

            /* Siniestro */
            $table->string('remito')->nullable();
            $table->string('workshop')->nullable();
            $table->unsignedBigInteger('ref_jazz_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            /* Relaciones */
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('type_id')->references('id')->on('tables')->onDelete('cascade');
            $table->foreign('payment_method_id')->references('id')->on('tables')->onDelete('cascade');
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
