<?php

namespace App\Http\TraitsControllers;

use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\PedidoCliente;
use App\Models\PedidoOnline;
use App\Models\Siniestro;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait TraitPedidos
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $siniestro = Table::where('name', 'order_type')->where('value', 'siniestro')->first();

        // Traer todos los pedidos que no sean de tipo "siniestro" ordenados por `estimated_date`
        $pedidos = Order::where('type_id', '!=', $siniestro->id)->get();

        // Ordenar los pedidos de acuerdo a las reglas específicas de cada estado
        $pedidos = $pedidos->sortBy(function ($order) {
            return [
                'incompleto' => 1,
                'pendiente' => 2,
                'retirar' => 3,
                'entregado' => 4,
                'envio' => 4,
                'cancelado' => 4,
            ][$order->getGeneralState()->value];
        })->groupBy(function ($order) {
            // Agrupar los pedidos por el estado general
            return $order->getGeneralState()->value;
        });

        // Aplicar el orden específico dentro de cada grupo de estado
        $pedidosOrdenados = collect();

        // Incompletos: Ordenados por fecha estimada, de más reciente a más antigua
        if ($pedidos->has('incompleto')) {
            $pedidosOrdenados = $pedidosOrdenados->concat(
                $pedidos['incompleto']->sortByDesc('estimated_date')
            );
        }

        // Pendientes: Ordenados por fecha de creación, de más antigua a más reciente
        if ($pedidos->has('pendiente')) {
            $pedidosOrdenados = $pedidosOrdenados->concat(
                $pedidos['pendiente']->sortBy('created_at')
            );
        }

        // Listo para retirar: Ordenados por fecha de creación, de más antigua a más reciente
        if ($pedidos->has('retirar')) {
            $pedidosOrdenados = $pedidosOrdenados->concat(
                $pedidos['retirar']->sortBy('created_at')
            );
        }

        // Entregados, Cancelados y Envíos: Ordenados por fecha de creación, de más reciente a más antigua
        $estadosFinales = ['entregado', 'cancelado', 'envio'];
        foreach ($estadosFinales as $estado) {
            if ($pedidos->has($estado)) {
                $pedidosOrdenados = $pedidosOrdenados->concat(
                    $pedidos[$estado]->sortByDesc('created_at')
                );
            }
        }

        // Convertir los pedidos ordenados a un recurso de colección
        $pedidos = OrderResource::collection($pedidosOrdenados);

        return sendResponse($pedidos);
    }

    public static function saveOrder(Request $request)
    {
        $user = auth()->user();

        $data = $request->all();
        $data['user_id'] = $user->id;

        $order = Order::create($data);

        /* Intentamos guardar lss ordernes productos */
        if (!self::storeOrderProduct($request, $order->id)) {
            DB::rollBack();
            throw new \Exception('No se pudieron guardar los productos del pedido cliente');
        }

        return $order;
    }

    public function showPedido(Request $requets, $id)
    {
        $order = Order::findOrFail($id);
        if ($requets->type) {
            $method = $requets->type;
            return sendResponse(OrderResource::$method($order));
        }
        return sendResponse(new OrderResource($order, 'complete'));
    }

    /*  public function showPedido($id)
    {
        $order = Order::findOrFail($id);
        return sendResponse(new OrderResource($order, 'complete'));
    } */

    public function updateState(Request $request)
    {
        DB::beginTransaction();

        try {
            $order = Order::find($request->order_id);

            if ($order->shipment) {
                return sendResponse(null, 'Ya existe un envio creado con este pedido', 300);
            }

            $type = $order->type->value;

            $general_state = $order->getGeneralState();

            $entregado = Table::where('name', "order_{$type}_state")->where('value', 'entregado')->first();
            $cancelado = Table::where('name', "order_{$type}_state")->where('value', 'cancelado')->first();

            if ($general_state->id == $entregado->id || $general_state->id == $cancelado->id) {
                return sendResponse(null, "El pedido se encuentra $general_state->value ", 301);
            }

            $detail = OrderProduct::where('order_id', $request->order_id)->get();
            foreach ($detail as $item) {
                /* Verificamos que cada item no tenga el estado de entregado */
                if ($item->state_id != $entregado->id && $item->state_id != $cancelado->id) {
                    $item->state_id = (int)$request->state_id;
                    $item->save();
                }
            }

            $estado = Table::find($request->state_id);
            activity("pedido.$estado->value")
                ->performedOn($order)
                ->withProperties(['state_id' => $request->state_id])
                ->log($request->motivo ? $request->motivo : "Pedido $estado->value");

            $order = Order::find($request->order_id);

            DB::commit();

            return sendResponse(new OrderResource($order, 'complete'));
        } catch (\Exception $e) {
            DB::rollBack();
            return sendResponse(null, $e->getMessage(), 300, $request->all());
        }
    }

    public function updatePedido(UpdateOrderRequest $request, int $id)
    {
        $type = Table::find($request->type_id);

        if ($type->value == 'cliente') {
            $pedido = PedidoCliente::findOrFail($id);
        } else if ($type->value == 'online') {
            $pedido = PedidoOnline::findOrFail($id);
        } else if ($type->value == 'siniestro') {
            $pedido = Siniestro::findOrFail($id);
        }

        $pedido->fill($request->all())->save();
        return sendResponse(OrderResource::toForm($pedido));
    }
}
