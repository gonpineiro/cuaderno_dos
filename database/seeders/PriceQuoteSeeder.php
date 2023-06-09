<?php

namespace Database\Seeders;

use App\Models\Order;
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
        PriceQuote::factory()->times(3)->create()->each(function ($price_quote) {
            $order = Order::find($price_quote->order_id);
            $order->price_quote_id = $price_quote->id;
            $order->save();

            for ($i = 1; $i <= 4; $i++) {
                if (isEven($i)) {
                    $this->createOrderProduct($price_quote, $i, 'product_id');
                } else {
                    /* $this->createOrderProduct($price_quote, $i, 'other_id'); */
                }
            }
        });
    }

    private function createOrderProduct($price_quote, $int, $price_quote_type)
    {
        $price_quote = [
            'price_quote_id' => $price_quote->id,
            $price_quote_type => $int,
            'state_id' => rand(13, 14),

            'amount' => rand(1, 6),
            'unit_price' => rand(500, 80000),
            'description' => "Detalle: $price_quote->id",
        ];
        PriceQuoteProduct::create($price_quote);
    }
}
