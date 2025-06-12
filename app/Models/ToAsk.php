<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ToAsk extends Model
{
    use SoftDeletes;
    protected $table = 'to_ask';

    protected $fillable = [
        'order_product_id',
        'product_id',
        'provider_id',
        'purchase_order',
        'user_id',
        'amount',
    ];

    protected $hidden = [
        'order_product_id',
        'product_id',
        'provider_id',
        'purchase_order',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function order_product()
    {
        return $this->belongsTo(OrderProduct::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}
