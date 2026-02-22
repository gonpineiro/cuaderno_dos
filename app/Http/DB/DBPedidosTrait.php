<?php

namespace App\Http\DB;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Table;
use Illuminate\Database\Eloquent\Builder;

trait DBPedidosTrait
{
    /**
     * Obtiene el query base de pedidos ordenados por prioridad de estado
     */
    protected function getPedidosQuery(): Builder
    {
        $siniestroId = $this->getSiniestroTypeId();

        $query = Order::query()
            ->join('tables as states', 'states.id', '=', 'orders.state_id')
            ->where('orders.type_id', '!=', $siniestroId)
            ->select('orders.*')
            ->with([
                'state:id,value',
                'shipment.state:id,value',
                'user:id,name',
                'client:id,name',
                'vehiculo:id,name',
                'payment_method:id,description',
            ]);

        $this->applyOrderPriority($query);

        return $query;
    }

    /**
     * Obtiene el query base de productos de pedidos ordenados por el estado del pedido
     */
    protected function getProductosPedidosQuery(int $limit = 1000): Builder
    {
        $query = OrderProduct::query()
            ->join('orders', 'orders.id', '=', 'order_product.order_id')
            ->join('tables as states', 'states.id', '=', 'orders.state_id')
            ->select('order_product.*')
            ->with([
                'product.provider:id,name',
                'product.brand:id,name',
                'product.activities',
                'order:id,state_id,shipment_id',
                'order.state:id,value,background_color',
                'order.shipment:id,state_id',
                'order.shipment.state:id,value',
                'order.type:id,value',
            ])
            ->limit($limit);

        $this->applyOrderPriority($query);

        return $query;
    }

    /**
     * Aplica el orden global por prioridad de estado + orden interno
     */
    protected function applyOrderPriority(Builder $query): void
    {
        // Prioridad de estado
        $query->orderByRaw("
            CASE states.value
                WHEN 'incompleto' THEN 1
                WHEN 'pendiente'  THEN 2
                WHEN 'retirar'    THEN 3
                WHEN 'entregado'  THEN 4
                WHEN 'cancelado'  THEN 4
                WHEN 'envio'      THEN 4
                ELSE 5
            END
        ");

        // Orden interno ASC
        $query->orderByRaw("
            CASE
                WHEN states.value = 'incompleto'
                    THEN orders.estimated_date
                WHEN states.value IN ('pendiente','retirar')
                    THEN orders.created_at
                ELSE NULL
            END ASC
        ");

        // Orden interno DESC para finales
        $query->orderByRaw("
            CASE
                WHEN states.value IN ('entregado','cancelado','envio')
                    THEN orders.created_at
                ELSE NULL
            END DESC
        ");
    }

    /**
     * Cachea el ID del tipo de pedido "siniestro"
     */
    protected function getSiniestroTypeId(): int
    {
        static $siniestroId = null;

        if ($siniestroId === null) {
            $siniestroId = (int) Table::where('name', 'order_type')
                ->where('value', 'siniestro')
                ->value('id');
        }

        return $siniestroId;
    }
}
