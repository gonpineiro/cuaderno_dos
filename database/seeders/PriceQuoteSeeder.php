<?php

namespace Database\Seeders;

use App\Models\PriceQuote;
use App\Models\PriceQuoteProduct;
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

            for ($i = 1; $i <= 10; $i++) {

                if (isEven($i)) {
                    $this->createOrderProduct($order, $i, 'product_id');
                } else {
                    $this->createOrderProduct($order, $i, 'other_id');
                }
            }
        });
    }

    private function createOrderProduct($order, $int, $order_type)
    {
        $price_quote = [
            'price_quote_id' => $order->id,
            $order_type => $int,
            'state_id' => rand(13, 14),

            'amount' => rand(1, 6),
            'unit_price' => rand(500, 80000),
            'description' => "Detalle: $order->id",
        ];
        PriceQuoteProduct::create($price_quote);
    }
}
