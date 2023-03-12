<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tables')->insert(['name' => 'brand', 'code' => '1', 'value' => 'FIAT']);
        DB::table('tables')->insert(['name' => 'brand', 'code' => '2', 'value' => 'RENO']);
        DB::table('tables')->insert(['name' => 'brand', 'code' => '3', 'value' => 'CITROEN']);
        DB::table('tables')->insert(['name' => 'brand', 'code' => '4', 'value' => 'BMW']);
        DB::table('tables')->insert(['name' => 'brand', 'code' => '5', 'value' => 'ALGO']);

        DB::table('tables')->insert(['name' => 'order_type', 'code' => '1', 'value' => 'online']);
        DB::table('tables')->insert(['name' => 'order_type', 'code' => '2', 'value' => 'simple']);

        DB::table('tables')->insert(['name' => 'order_state', 'code' => '1', 'value' => 'pendiente']);
        DB::table('tables')->insert(['name' => 'order_state', 'code' => '2', 'value' => 'avisado']);
        DB::table('tables')->insert(['name' => 'order_state', 'code' => '3', 'value' => 'cancelado']);
        DB::table('tables')->insert(['name' => 'order_state', 'code' => '4', 'value' => 'rechazado']);
    }
}
