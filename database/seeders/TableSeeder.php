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
    }
}
