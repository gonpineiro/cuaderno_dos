<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siniestro extends Order
{
    use HasFactory, SoftDeletes;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'client_id',
        'price_quote_id',
        'type_id',

        'engine',
        'chasis',

        'remito',
        'workshop',

        'observation'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
        'user_id',
        'type_id',
        'client_id',
        'price_quote_id',
        'payment_method_id',
        'invoice_number',
        'estimated_date',
        'deposit',
        'updated_at',
        'deleted_at',
        'pivot',
    ];

    public function getPercentages()
    {

        $array['incompleto'] = $this->detail->sum(function ($a) {
            return  $a->state->value == 'incompleto';
        });

        $array['completo'] = $this->detail->sum(function ($a) {
            return  $a->state->value == 'completo';
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

        if ($array['incompleto'] > 0) {
            $array['estado_general'] = 'incompleto';
        } else if ($array['completo'] > 0) {
            $array['estado_general'] = 'completo';
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
        $incompleto = $detail->sum(function ($a) {
            return  $a->state->value == 'incompleto';
        });

        $completo = $detail->sum(function ($a) {
            return  $a->state->value == 'completo';
        });

        $entregado = $detail->sum(function ($a) {
            return  $a->state->value == 'entregado';
        });

        $cancelado = $detail->sum(function ($a) {
            return  $a->state->value == 'cancelado';
        });

        $estadoGeneral = '';

        if ($incompleto > 0) {
            $estadoGeneral = 'incompleto';
        } else if ($completo > 0) {
            $estadoGeneral = 'completo';
        } else if ($entregado > 0) {
            $estadoGeneral = 'entregado';
        } else if ($cancelado > 0) {
            $estadoGeneral = 'cancelado';
        }

        return $estadoGeneral;
    }
}
