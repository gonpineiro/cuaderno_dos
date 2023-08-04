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
        'information_source_id',
        'type_price_id',
        'observation'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
        'user_id',
        'client_id',
        'updated_at',
        'information_source_id',
        'type_price_id',
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

    public function type_price()
    {
        return $this->belongsTo(Table::class, 'type_price_id');
    }

    public function information_source()
    {
        return $this->belongsTo(Table::class, 'information_source_id');
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

    public function getToAsignAttribute()
    {
        if ($this->client->is_insurance) {
            return Table::where('name', 'order_type')->where('value', 'siniestro')->first();
        } else if ($this->products->contains('empty_stock', true)) {
            return Table::where('name', 'order_type')->where('value', 'cliente')->first();
        } else {
            return Table::where('name', 'order_type')->where('value', 'online')->first();
        }
    }
}
