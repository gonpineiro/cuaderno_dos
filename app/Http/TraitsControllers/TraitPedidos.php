<?php

namespace App\Http\TraitsControllers;

use App\Http\Requests\Order\StoreClienteOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\PedidoCliente;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait TraitPedidos
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $siniestro = Table::where('name', 'order_type')->where('value', 'siniestro')->first();
        $pedidos = Order::where('type_id', '!=', $siniestro->id)->get();

        /* $pedidos = $pedidos->sortBy(function ($order) {
            return [
                'pendiente' => 1,
                'recibido' => 2,
                'avisado' => 3,
                'retirar' => 4,
                'entregado' => 5,
                'cancelado' => 6,
            ][$order->getGeneralState()];
        }); */

        // Ordenar los pedidos dentro de cada grupo por estimated_date
        /*    $pedidos = $pedidos->groupBy('estado_general')->map(function ($group) {
            return $group->sortBy('estimated_date');
        })->collapse(); */

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

    public function updateState(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $order = Order::find($id);

            $type = $order->type->value;

            $detail = OrderProduct::where('order_id', $order->id)->get();
            if ($type === 'cliente') {

                $entregado = Table::where('name', 'order_cliente_state')->where('value', 'entregado')->first();

                foreach ($detail as $item) {
                    /* Verificamos que cada item no tenga el estado de entregado */
                    if ($item->state_id != $entregado->id) {
                        $item->state_id = (int)$request->id;
                        $item->save();
                    }
                }
            } else if ($type === 'online') {

                $entregado = Table::where('name', 'order_online_state')->where('value', 'entregado')->first();
                $cacelado = Table::where('name', 'order_online_state')->where('value', 'cancelado')->first();

                foreach ($detail as $item) {
                    /* Verificamos que cada item no tenga el estado de entregado o cancelado */
                    if ($item->state_id != $entregado->id && $item->state_id != $cacelado->id) {
                        $item->state_id = (int)$request->id;
                        $item->save();
                    }
                }
            }

            $order = Order::find($id);

            DB::commit();

            return sendResponse(new OrderResource($order, 'complete'));
        } catch (\Exception $e) {
            DB::rollBack();

            return sendResponse(null, $e->getMessage(), 300, $request->all());
        }
    }
}
