<?php

namespace App\Http\TraitsControllers;

use App\Http\Requests\Order\StoreClienteOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Models\OrderProduct;
use App\Models\PedidoCliente;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait TraitPedidosCliente
{
    public function indexPedidosCliente(): \Illuminate\Http\JsonResponse
    {
        $pedidos = PedidoCliente::where('type_id', 7)->get();

        $pedidos = $pedidos->sortBy(function ($order) {
            return [
                'pendiente' => 1,
                'recibido' => 2,
                'entregado' => 3,
                'cancelado' => 4,
            ][$order->getGeneralState()];
        });

        // Ordenar los pedidos dentro de cada grupo por estimated_date
        /*    $pedidos = $pedidos->groupBy('estado_general')->map(function ($group) {
            return $group->sortBy('estimated_date');
        })->collapse(); */

        $pedidos = OrderResource::collection($pedidos);

        return sendResponse($pedidos);
    }

    public static function saveClienteOrder(StoreClienteOrderRequest $request)
    {
        $user = auth()->user();

        $data = $request->all();
        $data['user_id'] = $user->id;

        $order = PedidoCliente::create($data);

        /* Intentamos guardar lss ordernes productos */
        if (!self::storeOrderProduct($request, $order->id)) {
            DB::rollBack();
            throw new \Exception('No se pudieron guardar los productos del pedido cliente');
        }

        return $order;
    }

    public function showPedidoCliente($id)
    {
        $order = PedidoCliente::findOrFail($id);
        return sendResponse(new OrderResource($order, 'complete'));
    }

    public function updateStateCliente(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $detail = OrderProduct::where('order_id', $id)->get();

            $entregado = Table::where('name', 'order_cliente_state')->where('value', 'entregado')->first();
            foreach ($detail as $item) {
                /* Verificamos que cada item no tenga el estado de entregado */
                if ($item->state_id != $entregado->id) {
                    $item->state_id = (int)$request->id;
                    $item->save();
                }
            }

            $order = PedidoCliente::find($id);

            DB::commit();

            return sendResponse(new OrderResource($order, 'complete'));
        } catch (\Exception $e) {
            DB::rollBack();

            return sendResponse(null, $e->getMessage(), 300, $request->all());
        }
    }
}
