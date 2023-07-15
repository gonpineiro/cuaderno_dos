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
        /* 1 - 5 */
        DB::table('tables')->insert(['name' => 'brand', 'value' => 'FIAT']);
        DB::table('tables')->insert(['name' => 'brand', 'value' => 'RENO']);
        DB::table('tables')->insert(['name' => 'brand', 'value' => 'CITROEN']);
        DB::table('tables')->insert(['name' => 'brand', 'value' => 'BMW']);
        DB::table('tables')->insert(['name' => 'brand', 'value' => 'ALGO']);

        /* 6 | Productos existentes  */
        DB::table('tables')->insert(['name' => 'order_type', 'value' => 'online']);

        /* 7 | Productos no existentes */
        DB::table('tables')->insert(['name' => 'order_type_DELETE', 'value' => 'cliente']);

        /* 8 | Productos existentes | no existentes  */
        DB::table('tables')->insert(['name' => 'order_type', 'value' => 'siniestro']);

        /* 9 - 12 */
        DB::table('tables')->insert(['name' => 'order_state', 'value' => 'pendiente', 'background_color' => '#dc3545', 'color' => 'white']);
        DB::table('tables')->insert(['name' => 'order_state', 'value' => 'retirar', 'background_color' => '#ffc107', 'color' => 'black']);
        DB::table('tables')->insert(['name' => 'order_state', 'value' => 'entregado', 'background_color' => '#198754', 'color' => 'white']);
        DB::table('tables')->insert(['name' => 'order_state', 'value' => 'cancelado', 'background_color' => '#6c757d', 'color' => 'black']);

        /* 13 - 16 */
        DB::table('tables')->insert(['name' => 'order_siniestro_state', 'value' => 'pendiente', 'background_color' => '#dc3545', 'color' => 'white']);
        DB::table('tables')->insert(['name' => 'order_siniestro_state', 'value' => 'entregado', 'background_color' => '#ffc107', 'color' => 'black']);
        DB::table('tables')->insert(['name' => 'order_siniestro_state', 'value' => 'recibido', 'background_color' => '#198754', 'color' => 'white']);
        DB::table('tables')->insert(['name' => 'order_siniestro_state', 'value' => 'cancelado', 'background_color' => '#6c757d', 'color' => 'black']);

        /* 17 - 18 */
        DB::table('tables')->insert(['name' => 'price_quote_state', 'value' => 'cotizar', 'background_color' => '#198754', 'color' => 'white']);
        DB::table('tables')->insert(['name' => 'price_quote_state', 'value' => 'no cotizar', 'background_color' => '#6c757d', 'color' => 'black']);
    }
}
