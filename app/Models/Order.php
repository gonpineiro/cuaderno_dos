<?php

namespace App\Models;

use App\Models\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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
        'state_id',
        'information_source_id',

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

        /* Relacion con Jazz */
        'ref_jazz_id',
        'numero_jazz',

        'observation'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
        'user_id',
        'type_id',
        'client_id',
        'price_quote_id',
        'payment_method_id',
        'ref_jazz_id',
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

    public function detail_()
    {
        return $this->hasMany(OrderProduct::class)->whereHas('state', function ($query) {
            $query->where('value', '!=', 'cancelado');
        });
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

    public function information_source()
    {
        return $this->belongsTo(Table::class, 'information_source_id');
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

    public function state()
    {
        return $this->belongsTo(Table::class);
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

    /**
     * Obtiene la informacion basica para adjuntar a un pedido del Jazz
     * @param int $nroInterno | id del pedido del jazz,
     */
    public function getJazzData($nroInterno)
    {
        return $this->detail
            ->map(function ($detail) use ($nroInterno) {
                return [
                    "id"   => $detail->id,
                    'product_id' => $detail->product_id,
                    "idProducto"  => $detail->product->idProducto ?? null,
                    "precio" => $detail->unit_price,
                    "cantidad" => $detail->amount,
                    "nroInterno" => $this->ref_jazz_id ? $this->ref_jazz_id : $nroInterno
                ];
            })
            ->filter(fn($item) => !empty($item["idProducto"]))
            ->values()
            ->toArray();
    }

    /* public function getGeneralStateAttribute()
    {
        // 🚚 Si tiene envío → manda el estado del envío
        if ($this->shipment_id && $this->relationLoaded('shipment') && $this->shipment?->state) {
            return $this->shipment->state;
        }

        // 📦 Caso normal → estado del pedido
        return $this->state;
    } */



    public function getGeneralState()
    {
        if ($shipment = $this->shipment) {
            return (object) [
                'value' => 'envio',
                'description' => 'ENVÍO',
                'background_color' => '#0d6efd',
                'hover' => strtoupper($shipment->getGeneralState()->description),
                'url' =>  "/envios/$shipment->id",
            ];
        }

        $type = $this->type->value;
        switch ($type) {
            case 'online':
                return $this->calculateState('order_online_state');
            case 'cliente':
                return $this->calculateState('order_cliente_state');
            case 'siniestro':
                return $this->calculateState('order_siniestro_state');
            default:
                return null;
        }
    }

    private function calculateState($tableName)
    {
        // Cargar todos los detalles con su estado en una sola consulta
        $detail = $this->detail()->with('state')->get();

        // Agrupar estados y contar ocurrencias
        $stateCounts = $detail->groupBy('state.value')->map->count();

        // Definir prioridad de estados según el tipo de pedido
        $priority = [];
        switch ($tableName) {
            case 'order_online_state':
                $priority = ['pendiente', 'retirar', 'entregado', 'cancelado'];
                break;
            case 'order_cliente_state':
                $priority = ['incompleto', 'pendiente', 'retirar', 'entregado', 'cancelado'];
                break;
            case 'order_siniestro_state':
                $priority = ['incompleto', 'completo', 'entregado', 'cancelado'];
                break;
        }

        // Encontrar el primer estado que existe en la lista de prioridades
        $estadoGeneral = null;
        foreach ($priority as $state) {
            if (isset($stateCounts[$state])) {
                $estadoGeneral = $state;
                break;
            }
        }

        return Table::where('name', $tableName)->where('value', $estadoGeneral)->first();
    }

    public function setNumeroJazz(): void
    {
        try {
            $presupuesto = DB::connection('jazz')
                ->table('presupuestos')
                ->where('NroInterno', $this->ref_jazz_id)
                ->first();

            if (!$presupuesto) {
                throw new \Exception(
                    "No se encontró presupuesto en Jazz para NroInterno {$this->ref_jazz_id}"
                );
            }

            if (!isset($presupuesto->Numero)) {
                throw new \Exception(
                    "El presupuesto encontrado no contiene el campo Numero (NroInterno {$this->ref_jazz_id})"
                );
            }

            $this->numero_jazz = $presupuesto->Numero;
        } catch (\Throwable $e) {
            throw new \Exception(
                "Error al obtener Numero Jazz (Order ID {$this->id}): " . $e->getMessage()
            );
        }
    }
}
