<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'order_id',
        'client_id',

        'transport',
        'invoice_number',
        'nro_guia',
        'bultos',
        'send_adress',

        'observation'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
        'user_id',
        'order_id',
        'client_id',
        'updated_at',
        'deleted_at',
        'pivot',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function detail()
    {
        return $this->hasMany(ShipmentProduct::class, 'shipment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function price_quote()
    {
        return $this->belongsTo(PriceQuote::class, 'order_id', 'order_id');
    }

    public function payment_method()
    {
        return $this->belongsTo(Table::class, 'payment_method_id');
    }

    public function getPercentages()
    {

        $array['pendiente'] = $this->detail->sum(function ($a) {
            return  $a->state->value == 'pendiente';
        });

        $array['listo_enviar'] = $this->detail->sum(function ($a) {
            return  $a->state->value == 'listo_enviar';
        });

        $array['despachado'] = $this->detail->sum(function ($a) {
            return  $a->state->value == 'despachado';
        });

        $array['contrareemboldo'] = $this->detail->sum(function ($a) {
            return  $a->state->value == 'contrareemboldo';
        });

        $array['cancelado'] = $this->detail->sum(function ($a) {
            return  $a->state->value == 'cancelado';
        });

        $count = count($this->detail);

        foreach ($array as $key => $value) {
            $array[$key] = ($value * 100) / $count;
        }

        if ($array['pendiente'] > 0) {
            $array['estado_general'] = 'pendiente';
        } else if ($array['listo_enviar'] > 0) {
            $array['estado_general'] = 'listo_enviar';
        } else if ($array['despachado'] > 0) {
            $array['estado_general'] = 'despachado';
        } else if ($array['contrareemboldo'] > 0) {
            $array['estado_general'] = 'contrareemboldo';
        } else if ($array['cancelado'] > 0) {
            $array['estado_general'] = 'cancelado';
        }

        return $array;
    }

    public function getGeneralState()
    {
        $detail = $this->detail;
        $pendiente = $detail->sum(function ($a) {
            return  $a->state->value == 'pendiente';
        });

        $listo_enviar = $detail->sum(function ($a) {
            return  $a->state->value == 'listo_enviar';
        });

        $despachado = $detail->sum(function ($a) {
            return  $a->state->value == 'despachado';
        });

        $contrareemboldo = $detail->sum(function ($a) {
            return  $a->state->value == 'contrareemboldo';
        });

        $cancelado = $detail->sum(function ($a) {
            return  $a->state->value == 'cancelado';
        });

        $estadoGeneral = '';

        if ($pendiente > 0) {
            $estadoGeneral = 'pendiente';
        } else if ($listo_enviar > 0) {
            $estadoGeneral = 'listo_enviar';
        } else if ($despachado > 0) {
            $estadoGeneral = 'despachado';
        } else if ($contrareemboldo > 0) {
            $estadoGeneral = 'contrareemboldo';
        } else if ($cancelado > 0) {
            $estadoGeneral = 'cancelado';
        }

        return $estadoGeneral;
    }
}
