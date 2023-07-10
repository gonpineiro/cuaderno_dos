<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceQuote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'client_id',
        'engine',
        'chasis',
        'information_source',
        'type_price',
        'observation'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
        'user_id',
        'client_id',
        'updated_at',
        'observation',
        'pivot',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
    public function detail()
    {
        $productos = $this->hasMany(PriceQuoteProduct::class);
        return $productos;
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getTypePriceAttribute($value)
    {
        if ($value === 'lista') {
            return 'Precio Lista';
        } elseif ($value === 'contado') {
            return 'Precio Contado';
        }

        return $value;
    }
    public function getStateAttribute($value)
    {
        if ($value === 'online') {
            return 'Pedido Online';
        } elseif ($value === 'contado') {
            return 'Precio Contado';
        }

        return $value;
    }
}
