<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'provider_code',
        'factory_code',
        'equivalence',

        'description',
        'model',
        'engine',
        'observation',

        'min_stock',
        'empty_stock',

        'ship',
        'module',
        'side',
        'column',
        'row',

        'verified',

        'provider_id',
        'brand_id',
    ];

    protected $hidden = [
        'pivot',
        'provider_id',
        'brand_id',
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

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }
    public function orderProduct()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function priceQuoteProduct()
    {
        return $this->hasMany(PriceQuoteProduct::class);
    }

    public function brand()
    {
        return $this->belongsTo(Table::class);
    }

    public function getUbicationAttribute()
    {
        if (!$this->ship || !$this->module || !$this->side || !$this->column || !$this->row) {
            return null;
        }
        return $this->ship . $this->module . $this->side . $this->column . $this->row;
    }

    public function getStateAttribute()
    {
        if ($this->empty_stock) return 'empty_stock';

        if ($this->min_stock) return 'min_stock';

        if (!$this->min_stock && !$this->empty_stock) return 'ok';
    }
}
