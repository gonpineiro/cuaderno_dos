<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'amount',
    ];

    protected $hidden = [
        'purchase_order_id',
        'product_id',
    ];

    protected $table = 'purchase_order_product';

    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function purchase_order()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
