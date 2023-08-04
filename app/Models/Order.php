<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'client_id',
        'price_quote_id',
        'type_id',
        'engine',
        'chasis',
        'payment_method',
        'invoice_number',
        'remito',
        'workshop',
        'deposit',
        'estimated_date',
        'observation'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
        'user_id',
        'type_id',
        'client_id',
        'price_quote_id',
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
        return $this->hasMany(OrderProduct::class, 'order_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function type()
    {
        return $this->belongsTo(Table::class, 'type_id');
    }

    public function payment_method()
    {
        return $this->belongsTo(Table::class, 'payment_method_id');
    }

    /* public function getPercentages()
    {

        $array['pendiente'] = $this->detail->sum(function ($a) {
            return  $a->state->value == 'pendiente';
        });

        $array['retirar'] = $this->detail->sum(function ($a) {
            return  $a->state->value == 'retirar';
        });

        $array['entregado'] = $this->detail->sum(function ($a) {
            return  $a->state->value == 'entregado';
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
        } else if ($array['retirar'] > 0) {
            $array['estado_general'] = 'retirar';
        } else if ($array['entregado'] > 0) {
            $array['estado_general'] = 'entregado';
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

        $aRetirar = $detail->sum(function ($a) {
            return  $a->state->value == 'retirar';
        });

        $entregado = $detail->sum(function ($a) {
            return  $a->state->value == 'entregado';
        });

        $cancelado = $detail->sum(function ($a) {
            return  $a->state->value == 'cancelado';
        });

        $estadoGeneral = '';

        if ($pendiente > 0) {
            $estadoGeneral = 'pendiente';
        } else if ($aRetirar > 0) {
            $estadoGeneral = 'retirar';
        } else if ($entregado > 0) {
            $estadoGeneral = 'entregado';
        } else if ($cancelado > 0) {
            $estadoGeneral = 'cancelado';
        }

        return $estadoGeneral;
    } */
}
