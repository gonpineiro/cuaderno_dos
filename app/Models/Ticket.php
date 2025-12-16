<?php

namespace App\Models;

use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{

    use LogsActivity;

    protected $fillable = [
        'user_id',
        'responsable_id',
        'titulo',
        'descripcion',
        'resolucion',
        'estado_id',
        'prioridad_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function responsable()
    {
        return $this->belongsTo(User::class);
    }

    public function ticketable()
    {
        return $this->morphTo();
    }

    public function estado()
    {
        return $this->belongsTo(Table::class, 'estado_id');
    }

    public function prioridad()
    {
        return $this->belongsTo(Table::class, 'prioridad_id');
    }

    /** Retorna el modelo correspondiente en funcion del string dado */
    public static function modelMap($string_model)
    {
        $arrayMap = [
            'cotizacion'   => \App\Models\PriceQuote::class,
            'envio'        => \App\Models\Shipment::class,
            'pedido'       => \App\Models\Order::class,
            'producto'     => \App\Models\Product::class,
            'generico'     => null,
        ];

        return $arrayMap[$string_model];
    }

    public function getOrigenAttribute()
    {
        if (!$this->ticketable_type) {
            return 'generico';
        }
        $arrayMap = [
            'App\\Models\\PriceQuote' => 'cotizacion',
            'App\\Models\\Product' => 'producto'
        ];

        return $arrayMap[$this->ticketable_type];
    }
}
