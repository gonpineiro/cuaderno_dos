<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnPrinviceToProvinceId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            /* $table->dropColumn('province');
            $table->unsignedBigInteger('province_id');
            $table->foreign('province_id')->references('id')->on('tables')->onDelete('cascade'); */
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cities', function (Blueprint $table) {
            /* $table->dropForeign(['province_id']);
            $table->dropColumn('province_id');
            $table->string('province'); */
        });
    }
}
