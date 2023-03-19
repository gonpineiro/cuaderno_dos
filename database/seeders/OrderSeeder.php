<?php

namespace Database\Seeders;

use App\Models\Api\Order;
use App\Models\Api\OrderProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
        Order::factory()->times(50)->create(['type_id' => 6])->each(function ($order) {

            /* Por cada orden asociamos los 10 primeros productos */
            for ($i = 1; $i <= 10; $i++) {
                OrderProduct::create(['order_id' => $order->id, 'product_id' => $i, 'state_id' => rand(9, 12)]);
            }
        });

        /* Pedido */
        Order::factory()->times(50)->create(['type_id' => 7])->each(function ($order) {

            /* Por cada orden asociamos los 10 primeros productos */
            for ($i = 1; $i <= 10; $i++) {
                OrderProduct::create(['order_id' => $order->id, 'other_id' => $i, 'state_id' => rand(9, 12)]);
            }
        });

        /* siniestro */
        Order::factory()->times(50)->create(['type_id' => 8])->each(function ($order) {

            /* Por cada orden asociamos los 10 primeros productos */
            for ($i = 1; $i <= 10; $i++) {

                if (isEven($i)) {
                    OrderProduct::create(['order_id' => $order->id, 'product_id' => $i, 'state_id' => rand(9, 12)]);
                }else{
                    OrderProduct::create(['order_id' => $order->id, 'other_id' => $i, 'state_id' => rand(9, 12)]);
                }

            }
        });
    }
}
