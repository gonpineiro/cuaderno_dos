<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    use CommonTrait;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* Online */
        Order::factory()->times(100)->create()->each(function ($order) {
            $generatedValues = [];

            $cantidadProductos = rand(1, 5);
            for ($i = 1; $i <= $cantidadProductos; $i++) {
                do {
                    $valor = rand(1, 15);
                } while (in_array($valor, $generatedValues));

                $generatedValues[] = $valor;

                $product_detail =  $this->createProduct($order->id, 'order_id', rand(9, 12), $valor);
                OrderProduct::create($product_detail);
            }
        });
    }
}
