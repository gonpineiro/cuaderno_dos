<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('order_id');

            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->string('invoice_number')->nullable();

            $table->string('transport')->nullable();
            $table->string('nro_guia')->nullable();
            $table->string('bultos')->nullable();
            $table->string('send_adress')->nullable();

            $table->string('observation')->nullable();

            $table->timestamps();
            $table->softDeletes();

            /* Relaciones */
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('payment_method_id')->references('id')->on('tables')->onDelete('cascade');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('shipment_id')->references('id')->on('shipments');
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
            $table->dropForeign(['shipment_id']);
        });
        Schema::dropIfExists('shipments');
    }
}
