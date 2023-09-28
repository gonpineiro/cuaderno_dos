<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'provider_code',
        'factory_code',
        'equivalence',

        'description',
        'model',
        'engine',
        'observation',

        'ship',
        'module',
        'side',
        'column',
        'row',

        'verified',
        'is_special',

        'provider_id',
        'brand_id',
        'state_id',
    ];

    protected $hidden = [
        'pivot',
        'provider_id',
        'brand_id',
        'state_id',
        'created_at',
        'updated_at',
        'ship',
        'module',
        'side',
        'column',
        'row',
    ];

    protected $casts = [
        'min_stock' => 'boolean',
        'empty_stock' => 'boolean',
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function state()
    {
        return $this->belongsTo(Table::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }

    public function orderProduct()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function shipmentProduct()
    {
        return $this->hasMany(ShipmentProduct::class);
    }

    public function priceQuoteProduct()
    {
        return $this->hasMany(PriceQuoteProduct::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function getUbicationAttribute()
    {
        if (
            !$this->ship ||
            !$this->module ||
            !$this->side ||
            !$this->column ||
            !$this->row
        ) {
            return null;
        }

        // Formatear los valores a dos dígitos con ceros a la izquierda
        $column = sprintf('%02d', $this->column);
        $row = sprintf('%02d', $this->row);

        return $this->ship . $this->module . $this->side . $column . $row;
    }
}
