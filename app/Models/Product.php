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
        'updated_at'
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }

    public function brand()
    {
        return $this->belongsTo(Table::class);
    }
}
