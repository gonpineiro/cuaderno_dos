<?php

namespace Database\Seeders;

use App\Models\Api\Product;
use App\Models\Api\ProductOther;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::factory()->count(50)->create();
        ProductOther::factory()->count(50)->create();
    }
}
