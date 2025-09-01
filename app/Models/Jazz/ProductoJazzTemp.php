<?php

namespace App\Models\Jazz;

use App\Models\ProductJazz;

class ProductoJazzTemp extends ProductJazz
{
    protected $table = 'product_jazz_temp';

    protected $hidden = ['fecha_alta', 'fecha_mod', 'state'];

    public function brand()
    {
        return $this->belongsTo(ProductBrandJazz::class, 'codigo_marca', 'Codigo');
    }
}
