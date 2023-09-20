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

    public function shipment()
    {
        return $this->belongsTo(Shipment::class, 'order_id', 'order_id');
    }

    public function type_price()
    {
        return $this->belongsTo(Table::class, 'type_price_id');
    }

    public function information_source()
    {
        return $this->belongsTo(Table::class, 'information_source_id');
    }

    public function getStateAttribute()
    {
        $order = $this->order;

        if ($this->shipment) {
            return [
                'value' => 'envio',
                'string' => 'ENVÍO',
                'className' => 'primary',
                'url' =>  "/pedidos-envio/$order->id",
            ];
        }

        if ($order) {
            $type = $order->type->toArray();
            unset($type['id']);
            unset($type["background_color"]);
            unset($type["color"]);

            if ($type['value'] === 'online') {
                $type['string'] = 'COMPLETO';
                $type['className'] = 'success';
                $type['url'] = "/pedidos/$order->id";
            } else if ($type['value'] === 'cliente') {
                $type['string'] = 'INCOMPLETO';
                $type['className'] = 'success';
                $type['url'] = "/pedidos/$order->id";
            } else if ($type['value'] === 'siniestro') {
                $type['string'] =  'SINIESTRO';
                $type['className'] = 'success';
                $type['url'] = "/siniestro/$order->id";
            }
        } else {
            $type = [];

            $to_asign = $this->to_asign;

            $type['value'] = 'pendiente';
            $type['className'] = 'danger';

            if ($to_asign->value === 'cliente') {
                $type['string'] = 'INCOMPLETO S/A';
            } else if ($to_asign->value === 'online') {
                $type['string'] = 'COMPLETO S/A';
            } else if ($to_asign->value === 'siniestro') {
                $type['string'] = 'SINIESTRO S/A';
            }

            $fechaActual = \Carbon\Carbon::now();
            $diferenciaDias = $this->created_at->diffInDays($fechaActual);
            if ($diferenciaDias >= 7) {
                $type['value'] = 'vencido';
                $type['className'] = 'badge-vencido';
            }

            $type['url'] = null;
        }

        return $type;
    }

    public function getToAsignAttribute()
    {
        if ($this->client->is_insurance) {
            return Table::where('name', 'order_type')->where('value', 'siniestro')->first();
        } else if ($this->products->contains('is_special', true)) {
            return Table::where('name', 'order_type')->where('value', 'cliente')->first();
        } else {
            return Table::where('name', 'order_type')->where('value', 'online')->first();
        }
    }
}
