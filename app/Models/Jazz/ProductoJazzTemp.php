<?php

namespace App\Models\Jazz;

use App\Models\ProductJazz;

class ProductoJazzTemp extends ProductJazz
{
    protected $table = 'product_jazz_temp'; // opcional si el nombre de la tabla no sigue convención

    protected $hidden = ['fecha_alta', 'fecha_mod', 'state'];
}
