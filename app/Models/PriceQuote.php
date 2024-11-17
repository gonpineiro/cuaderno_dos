<?php

namespace App\Models;

use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceQuote extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'user_id',
        'client_id',

        'year',
        'chasis',
        'contacto',
        'vehiculo_id',

        'information_source_id',
        'type_price_id',
        'observation',
        'created_at'
    ];

    protected $dates = ['deleted_at'];

    protected static $logAttributes = [
        'user_id',
        'client_id',

        'year',
        'chasis',
        'contacto',
        'vehiculo_id',

        'information_source_id',
        'type_price_id',
        'observation',
    ];

    protected $hidden = [
        'user_id',
        'client_id',
        'vehiculo_id',
        'updated_at',
        'information_source_id',
        'type_price_id',
        'brand_id',
        'pivot',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class)->withTrashed();
    }

    public function detail()
    {
        $productos = $this->hasMany(PriceQuoteProduct::class);
        return $productos;
    }

    public function detail_cotizable()
    {
        return $this->hasMany(PriceQuoteProduct::class)->whereHas('state', function ($query) {
            $query->where('name', 'price_quote_state')->where('value', 'cotizar');
        });
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
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
    {;

        if ($shipment = $this->shipment) {
            return [
                'value' => 'envio',
                'string' => 'ENVÍO',
                'hover' => strtoupper($shipment->getGeneralState()->description),
                'className' => 'primary',
                'url' =>  "/envios/$shipment->id",
            ];
        }

        if ($order = $this->order) {
            $type = $order->type->toArray();
            unset($type['id']);
            unset($type["background_color"]);
            unset($type["color"]);

            if ($type['value'] === 'online' || $type['value'] === 'cliente') {
                $type['string'] = 'PEDIDO';
                $type['hover'] = strtoupper($order->getGeneralState()->description);
                $type['className'] = 'success';
                $type['url'] = "/pedidos/$order->id";
            } else if ($type['value'] === 'siniestro') {
                $type['string'] =  'SINIESTRO';
                $type['hover'] = strtoupper($order->getGeneralState()->description);
                $type['className'] = 'success';
                $type['url'] = "/siniestros/$order->id";
            }
        } else {
            $type = [];

            $type['value'] = 'pendiente';
            $type['className'] = 'warning';

            $type['string'] = 'PENDIENTE';

            $fechaActual = \Carbon\Carbon::now();
            $diferenciaDias = $this->created_at->diffInDays($fechaActual);
            if ($diferenciaDias >= 1) {
                $type['string'] = 'PENDIENTE';
                $type['value'] = 'vencido';
                $type['className'] = 'danger';
            }

            $type['url'] = null;
        }

        return $type;
    }

    public function getToAsignAttribute()
    {
        $no_cotizable = Table::where('name', 'price_quote_state')->where('value', 'no cotizar')->first();
        $hasSpecialProducts = $this->detail()->with('product')->get()
            ->contains(function ($item) use ($no_cotizable) {
                return $item->product->is_special && $item->state_id !== $no_cotizable->id;
            });

        if ($this->client->is_insurance) {
            return Table::where('name', 'order_type')->where('value', 'siniestro')->first();
        } else if ($hasSpecialProducts) {
            return Table::where('name', 'order_type')->where('value', 'cliente')->first();
        } else {
            return Table::where('name', 'order_type')->where('value', 'online')->first();
        }
    }

    public function getClientChasisAttribute()
    {
        return [
            'client_id' => $this->client_id,
            'chasis' => $this->chasis,
            'vehiculo_id' => $this->vehiculo_id,
            'year' => $this->year,
        ];
    }
}
