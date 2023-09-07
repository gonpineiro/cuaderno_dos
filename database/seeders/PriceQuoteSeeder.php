<?php

namespace Database\Seeders;

use App\Models\PriceQuote;
use App\Models\PriceQuoteProduct;
use App\Models\Product;
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
        PriceQuote::factory()->times(100)->create(['order_id' => null])->each(function ($price_quote) {

            $generatedValues = [];
            $cantidadProductos = rand(2, 10);
            for ($i = 1; $i <= $cantidadProductos; $i++) {
                do {
                    $product = Product::inRandomOrder()->first();
                    $valor = $product->id;
                } while (in_array($valor, $generatedValues));

                $generatedValues[] = $valor;

                $product_detail =  $this->createProduct($price_quote->id, 'price_quote_id', rand(28, 29), $valor);
                PriceQuoteProduct::create($product_detail);
            }
        });
    }
}
