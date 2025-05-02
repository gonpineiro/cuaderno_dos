<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductJazz extends Model
{

    protected $table = 'product_jazz';

    protected $fillable = [
        'idProducto',
        'nombre',
        'stock',
        'precio_lista_2',
        'precio_lista_3',
        'precio_lista_6',

        'fecha_alta',
        'fecha_mod'
    ];
}
