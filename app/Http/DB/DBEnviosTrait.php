<?php

namespace App\Http\DB;

use App\Models\Shipment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait DBEnviosTrait
{
    /**
     * Obtiene el query base de envios ordenados por prioridad de estado
     */
    protected function getEnviosQuery(): Builder
    {
        $query = Shipment::query()
            ->leftJoin('tables as shipment_state', 'shipment_state.id', '=', 'shipments.state_id')
            ->leftJoin('tables as payment_method', 'payment_method.id', '=', 'shipments.payment_method_id')

            ->select([
                'shipments.*',

                DB::raw("
                CASE
                    WHEN shipment_state.value IN ('pendiente','listo_enviar','cancelado')
                        THEN shipment_state.value

                    WHEN shipment_state.value = 'despachado'
                        AND payment_method.value = 'contrareembolso'
                        THEN 'despachado_contrareembolso'

                    WHEN shipment_state.value <> 'despachado'
                        AND payment_method.value = 'contrareembolso'
                        THEN 'contrareembolso_pago'

                    WHEN shipment_state.value = 'despachado'
                        AND payment_method.value = 'online'
                        THEN 'despachado_online'

                    ELSE shipment_state.value
                END AS estado_search
            "),
            ])

            ->orderByRaw("
                CASE estado_search
                    WHEN 'pendiente' THEN 1
                    WHEN 'listo_enviar' THEN 2
                    WHEN 'despachado_contrareembolso' THEN 3
                    WHEN 'contrareembolso_pago' THEN 4
                    WHEN 'despachado_online' THEN 5
                    WHEN 'contrareembolso' THEN 6
                    WHEN 'despachado' THEN 7
                    WHEN 'cancelado' THEN 8
                    ELSE 99
                END
        ")

            ->with([
                'user:id,name',
                'client',
                'payment_method',
                'state',
            ]);

        return $query;
    }
}
