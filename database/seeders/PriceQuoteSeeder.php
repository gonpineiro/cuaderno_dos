<?php

namespace Database\Seeders;

use App\Models\PriceQuote;
use Illuminate\Database\Seeder;

class PriceQuoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        /* Online */
        PriceQuote::factory()->times(100)->create()->each(function ($order) {


        });
    }
}
