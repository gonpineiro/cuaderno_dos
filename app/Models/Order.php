<?php

namespace App\Models;

use App\Models\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'user_id',
        'client_id',
        'price_quote_id',
        'shipment_id',
        'type_id',

        /* Generales */
        'year',
        'chasis',
        'contacto',
        'vehiculo_id',

        /* Pedido online */
        'payment_method_id',
        'invoice_number',

        /* Pedidos Cliente */
        'deposit',
        'payment_method_id',
        'estimated_date',

        /* Siniestro */
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
        'updated_at',
        'deleted_at',
        'pivot',
    ];

    public function products()
    {
        return $this->hasManyThrough(Product::class, OrderProduct::class);
    }

    public function detailPending()
    {
        return $this->hasMany(OrderProduct::class, 'order_id')
            ->whereHas('state', function ($query) {
                $query->whereNotIn('value', ['entregado', 'cancelado']);
            });
    }

    public function detail()
    {
        return $this->hasMany(OrderProduct::class, 'order_id');
    }

    public function client()

    {
        return $this->belongsTo(Client::class);
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
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

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function price_quote()
    {
        return $this->belongsTo(PriceQuote::class);
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject')->with('causer');
    }

    public function getUserCompleteAttribute()
    {
        $log = $this->activities()->where('log_name', 'like', 'pedido.%')->orderBy('id', 'DESC')->first();
        if (!$log) return null;

        return  [
            'user' => User::find($log->causer_id),
            'description' => $log->description,
            'date' => $log->created_at,
        ];
    }

    public function setShipmentState()
    {
        $type = $this->type->value;

        $query = Table::where("name", "order_" . "$type" . "_state");
        if ($type == 'siniestro') {
            $newState = $query->where('value', 'incompleto')->first();
        } else {
            $newState = $query->where('value', 'retirar')->first();
        }
        $cancelado = Table::where("name", "order_" . "$type" . "_state")->where('value', 'cancelado')->first();

        $this->detail->each(function ($order_product) use ($newState, $cancelado) {
            if ($order_product->state_id != $cancelado->id) {
                $order_product->state_id = $newState->id;
                $order_product->save();
            }
        });
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
    } */

    public function getGeneralState()
    {
        if ($shipment = $this->shipment) {
            return (object) [
                'value' => 'envio',
                'description' => 'ENVÍO',
                'background_color' => '#0d6efd',
                'hover' => strtoupper($shipment->getGeneralState()->description),
                //'className' => 'primary',
                'url' =>  "/envios/$shipment->id",
            ];
        }
        $type = $this->type->value;

        if ($type == 'online') {
            return $this->onlineState();
        }

        if ($type == 'cliente') {
            return $this->clienteState();
        }

        if ($type == 'siniestro') {
            return $this->siniestroState();
        }
    }

    private function onlineState()
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

        $estado = Table::where('name', 'order_online_state')->where('value', $estadoGeneral)->first();

        return $estado;
    }

    private function clienteState()
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

    private function siniestroState()
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

        $estado = Table::where('name', 'order_siniestro_state')->where('value', $estadoGeneral)->first();

        return $estado;
    }
}
