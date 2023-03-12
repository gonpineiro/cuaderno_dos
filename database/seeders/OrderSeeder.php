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
        Order::factory()->times(100)->create()->each(function ($order) {
            $id = $order->id;

            /* Por cada orden asociamos los 10 primeros productos */
            for ($i = 1; $i <= 10; $i++) {
                $order = OrderProduct::create(['order_id' => $id, 'product_id' => $i, 'state_id' => 8]);
            }
        });
    }
}
