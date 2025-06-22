<?php

namespace App\Models\Jazz;

use Illuminate\Database\Eloquent\Model;

class ProductoJazz extends Model
{
    protected $connection = 'jazz'; // usa la conexión definida en config/database.php como 'jazz'

    protected $table = 'productos'; // opcional si el nombre de la tabla no sigue convención

}
