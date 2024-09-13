<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductProvider extends Model
{
    protected $table = 'product_provider';

    protected $hidden = ['pivot'];
}
