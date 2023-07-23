<?php

namespace Database\Seeders;

use App\Models\OrderProduct;
use App\Models\Siniestro;
use Illuminate\Database\Seeder;

class SiniestroSeeder extends Seeder
{
    use CommonTrait;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Siniestro::factory()->times(10)->create()->each(function ($order) {
            $generatedValues = [];

            $cantidadProductos = rand(1, 5);
            for ($i = 1; $i <= $cantidadProductos; $i++) {
                do {
                    $valor = rand(1, 15);
                } while (in_array($valor, $generatedValues));

                $generatedValues[] = $valor;

                $product_detail =  $this->createProduct($order->id, 'order_id', rand(17, 20), $valor);
                OrderProduct::create($product_detail);
            }
        });
    }
}
