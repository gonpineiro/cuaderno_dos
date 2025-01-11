<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShipmentProduct extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'shipment_id',
        'state_id',
        'product_id',
        'provider_id',

        'amount',
        'unit_price',
        'description',
    ];

    protected $dates = ['deleted_at'];

    protected $table = 'shipment_product';

    public $timestamps = false;

    public function state()
    {
        return $this->belongsTo(Table::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}
