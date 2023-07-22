<?php

namespace Database\Seeders;

use App\Models\PriceQuote;
use App\Models\PriceQuoteProduct;
use Illuminate\Database\Seeder;

class PriceQuoteSeeder extends Seeder
{
    use CommonTrait;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* Sin asignar a un pedido */
        PriceQuote::factory()->times(10)->create(['order_id' => null])->each(function ($price_quote) {
            $generatedValues = [];

            $cantidadProductos = rand(1, 5);
            for ($i = 1; $i <= $cantidadProductos; $i++) {
                do {
                    $valor = rand(1, 50);
                } while (in_array($valor, $generatedValues));

                $generatedValues[] = $valor;

                // $this->createOrderProduct($price_quote, $valor);
                $product_detail =  $this->createProduct($price_quote->id, 'price_quote_id', rand(21, 22), $valor);
                PriceQuoteProduct::create($product_detail);
            }
        });

        /* Asignando a un pedido */
        /* PriceQuote::factory()->times(20)->create()->each(function ($price_quote) {
            $order = Order::find($price_quote->order_id);
            $order->price_quote_id = $price_quote->id;
            $order->save();

            $a = $order->toArray();

            $generatedValues = [];

            $cantidadProductos = rand(1, 5);
            for ($i = 1; $i <= $cantidadProductos; $i++) {
                do {
                    $valor = rand(1, 50);
                } while (in_array($valor, $generatedValues));

                $generatedValues[] = $valor;

                $product_detail =  $this->createProduct($price_quote->id, 'price_quote_id', rand(17, 18), $valor);
                PriceQuoteProduct::create($product_detail);
            }
        }); */
    }
}
