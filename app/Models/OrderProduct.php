<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderProduct extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'state_id',
        'product_id',
        'amount',
        'unit_price',
        'description'
    ];

    protected $hidden = [
        'order_id',
        'state_id',
        'product_id',
    ];

    protected $dates = ['deleted_at'];

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

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
