<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* Online */
        Order::factory()->times(10)->create()->each(function ($order) {
            $generatedValues = [];

            $cantidadProductos = rand(1, 10);
            for ($i = 1; $i <= $cantidadProductos; $i++) {
                do {
                    $valor = rand(1, 15);
                } while (in_array($valor, $generatedValues));

                $generatedValues[] = $valor;

                $this->createOrderProduct($order, $valor);
            }
        });
    }

    private function createOrderProduct($order, $int)
    {
        $orderProduct = [
            'order_id' => $order->id,
            'product_id' => $int,
            'state_id' => rand(9, 12),

            'amount' => rand(1, 6),
            'unit_price' => rand(500, 80000),
            'description' => "Detalle: $order->id",
        ];
        OrderProduct::create($orderProduct);
    }
}
