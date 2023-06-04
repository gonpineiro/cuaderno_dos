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
        DB::table('tables')->insert(['name' => 'brand', 'value' => 'FIAT']);
        DB::table('tables')->insert(['name' => 'brand', 'value' => 'RENO']);
        DB::table('tables')->insert(['name' => 'brand', 'value' => 'CITROEN']);
        DB::table('tables')->insert(['name' => 'brand', 'value' => 'BMW']);
        DB::table('tables')->insert(['name' => 'brand', 'value' => 'ALGO']);

        /* Productos existentes */
        DB::table('tables')->insert(['name' => 'order_type', 'value' => 'online']);

        /* Productos no existentes */
        DB::table('tables')->insert(['name' => 'order_type', 'value' => 'pedido']);

        /* Productos existentes | no existentes */
        DB::table('tables')->insert(['name' => 'order_type', 'value' => 'siniestro']);

        DB::table('tables')->insert(['name' => 'order_state', 'value' => 'pendiente', 'background_color' => 'red', 'color' => 'white']);
        DB::table('tables')->insert(['name' => 'order_state', 'value' => 'retirar', 'background_color' => 'yellow', 'color' => 'black']);
        DB::table('tables')->insert(['name' => 'order_state', 'value' => 'entregado', 'background_color' => 'green', 'color' => 'white']);
        DB::table('tables')->insert(['name' => 'order_state', 'value' => 'cancelado', 'background_color' => 'gray', 'color' => 'black']);
    }
}
