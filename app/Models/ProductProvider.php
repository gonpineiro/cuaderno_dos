<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductProvider extends Model
{
    protected $table = 'product_provider';

    protected $fillable = [
        'product_id',
        'provider_id',
        'provider_code',
        'is_habitual',
    ];
    protected $hidden = ['pivot', 'created_at', 'updated_at'];
}
