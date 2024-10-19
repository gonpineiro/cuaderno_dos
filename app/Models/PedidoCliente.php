<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PedidoCliente extends Order
{
    use HasFactory, SoftDeletes;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'client_id',
        'price_quote_id',
        'type_id',

        /* Generales */
        'year',
        'chasis',
        'contacto',
        'vehiculo_id',

        'deposit',
        'payment_method_id',
        'estimated_date',

        'observation'
    ];

    protected static $logAttributes = [
        'user_id',
        'client_id',
        'price_quote_id',
        'type_id',

        /* Generales */
        'year',
        'chasis',
        'contacto',
        'vehiculo_id',

        'deposit',
        'payment_method_id',
        'estimated_date',

        'observation'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
        'user_id',
        'type_id',
        'client_id',
        'price_quote_id',
        'payment_method_id',
        'remito',
        'workshop',
        'updated_at',
        'invoice_number',
        'deleted_at',
        'pivot',
    ];

    public function getPercentages()
    {

        $array['pendiente'] = $this->detail->sum(function ($a) {
            return  $a->state->value == 'pendiente';
        });

        $array['recibido'] = $this->detail->sum(function ($a) {
            return  $a->state->value == 'recibido';
        });

        $array['avisado'] = $this->detail->sum(function ($a) {
            return  $a->state->value == 'avisado';
        });

        $array['entregado'] = $this->detail->sum(function ($a) {
            return  $a->state->value == 'entregado';
        });

        $count = count($this->detail);

        foreach ($array as $key => $value) {
            $array[$key] = ($value * 100) / $count;
        }

        if ($array['pendiente'] > 0) {
            $array['estado_general'] = 'pendiente';
        } else if ($array['recibido'] > 0) {
            $array['estado_general'] = 'recibido';
        } else if ($array['avisado'] > 0) {
            $array['estado_general'] = 'avisado';
        } else if ($array['entregado'] > 0) {
            $array['estado_general'] = 'entregado';
        }

        return $array;
    }

    public function getGeneralState()
    {
        $detail = $this->detail;
        $incompleto = $detail->sum(function ($a) {
            return  $a->state->value == 'incompleto';
        });

        $pendiente = $detail->sum(function ($a) {
            return  $a->state->value == 'pendiente';
        });

        $retirar = $detail->sum(function ($a) {
            return  $a->state->value == 'retirar';
        });

        $cancelado = $detail->sum(function ($a) {
            return  $a->state->value == 'cancelado';
        });

        $entregado = $detail->sum(function ($a) {
            return  $a->state->value == 'entregado';
        });

        $estadoGeneral = '';

        if ($incompleto > 0) {
            $estadoGeneral = 'incompleto';
        } else if ($pendiente > 0) {
            $estadoGeneral = 'pendiente';
        } else if ($retirar > 0) {
            $estadoGeneral = 'retirar';
        } else if ($entregado > 0) {
            $estadoGeneral = 'entregado';
        } else if ($cancelado > 0) {
            $estadoGeneral = 'cancelado';
        }

        $estado = Table::where('name', 'order_cliente_state')->where('value', $estadoGeneral)->first();

        return $estado;
    }
}
