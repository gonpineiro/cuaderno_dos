<?php

namespace App\Http\DB;

use App\Models\Order;
use App\Models\Table;

trait DBPedidosTrait
{
    private function getPedidosQuery()
    {
        $siniestroId = Table::where('name', 'order_type')
            ->where('value', 'siniestro')
            ->value('id');

        $query =  Order::query()
            ->join('tables as states', 'states.id', '=', 'orders.state_id')
            ->where('orders.type_id', '!=', $siniestroId)
            ->with([
                'state:id,value',
                'user:id,name',
                'client:id,name',
                'vehiculo:id,name',
                'payment_method:id,description',
            ])
            ->select('orders.*')

            /* 1️⃣ PRIORIDAD DE ESTADO */
            ->orderByRaw("
            CASE states.value
                WHEN 'incompleto' THEN 1
                WHEN 'pendiente' THEN 2
                WHEN 'retirar' THEN 3
                WHEN 'entregado' THEN 4
                WHEN 'cancelado' THEN 4
                WHEN 'envio' THEN 4
                ELSE 5
            END
        ")

            /* 2️⃣ ORDEN INTERNO (ASC) PARA INCOMPLETO / PENDIENTE / RETIRAR */
            ->orderByRaw("
            CASE
                WHEN states.value = 'incompleto' THEN orders.estimated_date
                WHEN states.value IN ('pendiente','retirar') THEN orders.created_at
                ELSE NULL
            END ASC
        ")

            /* 3️⃣ ORDEN INTERNO (DESC) PARA ESTADOS FINALES */
            ->orderByRaw("
            CASE
                WHEN states.value IN ('entregado','cancelado','envio')
                THEN orders.created_at
                ELSE NULL
            END DESC
        ");

        return $query;
    }
}
