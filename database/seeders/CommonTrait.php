<?php

namespace Database\Seeders;

trait CommonTrait
{

    private function createProduct(int $id, string  $primary, int $state_id, int $product_id)
    {
        return [
            $primary => $id,
            'state_id' => $state_id,

            'product_id' => $product_id,
            'amount' => rand(1, 6),
            'unit_price' => rand(500, 80000),
            'description' => "Detalle: $id",
        ];
    }
}
