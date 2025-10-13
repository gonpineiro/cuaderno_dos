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
        'provider_id',
        'amount',
        'purchase_order',
        'unit_price',
        'description',

        /* Relacion con Jazz */
        'ref_jazz_id',
    ];

    protected $hidden = [
        'state_id',
        'product_id',
        'provider_id',
        'purchase_order',
        'ref_jazz_id',
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
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
