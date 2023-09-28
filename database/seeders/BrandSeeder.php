<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('brands')->insert(['name' => 'FIAT']);
        DB::table('brands')->insert(['name' => 'RENO']);
        DB::table('brands')->insert(['name' => 'CITROEN']);
        DB::table('brands')->insert(['name' => 'BMW']);
        DB::table('brands')->insert(['name' => 'TOYOTA']);
    }
}
