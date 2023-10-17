<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToAsk extends Model
{
    protected $table = 'to_ask';

    public $timestamps = false;

    protected $fillable = [
        'order_product_id',
        'product_id',
        'purchase_order',
        'amount',
    ];

    protected $hidden = [
        'order_product_id',
        'product_id',
        'purchase_order',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order_product()
    {
        return $this->belongsTo(OrderProduct::class);
    }
}
