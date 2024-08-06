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
        $pedidos = Order::where('type_id', '!=', $siniestro->id)->orderBy('estimated_date')->get();

        $pedidos = $pedidos->sortBy(function ($order) {
            return [
                'incompleto' => 1,
                'pendiente' => 2,
                'retirar' => 3,
                'entregado' => 4,
                'cancelado' => 5,
            ][$order->getGeneralState()->value];
        });

        $pedidos = OrderResource::collection($pedidos);

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

    public function showPedido($id)
    {
        $order = Order::findOrFail($id);
        return sendResponse(new OrderResource($order, 'complete'));
    }

    public function updateState(Request $request)
    {
        DB::beginTransaction();

        try {
            $order = Order::find($request->order_id);

            if ($order->shipment) {
                return sendResponse(null, 'Ya existe un envio creado con este pedido', 300);
            }

            $type = $order->type->value;

            $detail = OrderProduct::where('order_id', $request->order_id)->get();
            if ($type === 'cliente') {

                $entregado = Table::where('name', 'order_cliente_state')->where('value', 'entregado')->first();
                $entregado = Table::where('name', 'order_cliente_state')->where('value', 'cancelado')->first();

                foreach ($detail as $item) {
                    /* Verificamos que cada item no tenga el estado de entregado */
                    if ($item->state_id != $entregado->id) {
                        $item->state_id = (int)$request->state_id;
                        $item->save();
                    }
                }
            } else if ($type === 'online') {

                $entregado = Table::where('name', 'order_online_state')->where('value', 'entregado')->first();
                $cacelado = Table::where('name', 'order_online_state')->where('value', 'cancelado')->first();

                foreach ($detail as $item) {
                    /* Verificamos que cada item no tenga el estado de entregado o cancelado */
                    if ($item->state_id != $entregado->id && $item->state_id != $cacelado->id) {
                        $item->state_id = (int)$request->state_id;
                        $item->save();
                    }
                }
            }

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
        return sendResponse(new OrderResource($pedido, 'complete'));
    }
}
