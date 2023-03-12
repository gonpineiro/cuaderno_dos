<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'state_id'
    ];

    protected $table = 'order_product';

    public $timestamps = false;

    public function state()
    {
        return $this->belongsTo(Table::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
