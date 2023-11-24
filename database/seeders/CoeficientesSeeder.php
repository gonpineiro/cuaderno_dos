<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoeficientesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('coeficientes')->insert(['description' => 'CONTADO / DEB / 1 CUOTA', 'value' => 1.02]);
        DB::table('coeficientes')->insert(['description' => 'AHORA 3', 'value' => 1.2272, 'cuotas' => 3]);
        DB::table('coeficientes')->insert(['description' => 'AHORA 6', 'value' => 1.39641, 'cuotas' => 6]);
        DB::table('coeficientes')->insert(['description' => 'AHORA 12', 'value' => 1.89696, 'cuotas' => 12]);
        DB::table('coeficientes')->insert(['description' => 'OTRAS 3C / CREDICOM', 'value' => 1.39641000, 'cuotas' => 3]);
        DB::table('coeficientes')->insert(['description' => 'MERCADO PAGO', 'value' => 1.02]);
    }
}
