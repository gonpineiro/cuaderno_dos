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
        Order::factory()->times(5)->create(['type_id' => 6])->each(function ($order) {

            for ($i = 1; $i <= 10; $i++) {
                $this->createOrderProduct($order, $i);
            }
        });

        /* Pedido */
        Order::factory()->times(3)->create(['type_id' => 7])->each(function ($order) {

            for ($i = 1; $i <= 10; $i++) {
                $this->createOrderProduct($order, $i);
            }
        });

        /* siniestro */
        Order::factory()->times(3)->create(['type_id' => 8])->each(function ($order) {

            for ($i = 1; $i <= 10; $i++) {
                $this->createOrderProduct($order, $i);
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
