<?php

namespace Database\Seeders;

use App\Models\Api\Order;
use App\Models\Api\OrderProduct;
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
        Order::factory()->times(30)->create(['type_id' => 6])->each(function ($order) {

            /* Por cada orden asociamos los 10 primeros productos */
            for ($i = 1; $i <= 10; $i++) {
                $this->createOrderProduct($order, $i, 'product_id');
            }
        });

        /* Pedido */
        Order::factory()->times(30)->create(['type_id' => 7])->each(function ($order) {

            /* Por cada orden asociamos los 10 primeros productos */
            for ($i = 1; $i <= 10; $i++) {
                $this->createOrderProduct($order, $i, 'other_id');
            }
        });

        /* siniestro */
        Order::factory()->times(30)->create(['type_id' => 8])->each(function ($order) {

            /* Por cada orden asociamos los 10 primeros productos */
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
        $orderProduct = [
            'order_id' => $order->id,
            $order_type => $int,
            'state_id' => rand(9, 12),

            'amount' => rand(1, 6),
            'unit_price' => rand(500, 80000),
            'detalle' => "Detalle: $order->id",
        ];
        OrderProduct::create($orderProduct);
    }
}
