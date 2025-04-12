<?php

namespace App\Http\TraitsControllers;

use App\Http\Requests\Order\StoreOnlineOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Models\Coeficiente;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\PedidoCliente;
use App\Models\PedidoOnline;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait TraitPedidosOnline
{
    public function indexOnlines(): \Illuminate\Http\JsonResponse
    {
        $order = OrderResource::collection(PedidoOnline::where('type_id', 6)->get());
        return sendResponse($order);
    }

    public static function saveOnlineOrder(StoreOnlineOrderRequest $request)
    {
        $user = auth()->user();

        $data = $request->all();
        $data['user_id'] = $user->id;

        $order = PedidoOnline::create($data);

        $is_contado = $order->price_quote->type_price->value == 'contado';
        $contado_deb = $is_contado ? Coeficiente::find(2) : null;

        /* Intentamos guardar lss ordernes productos */
        $redondear = $order->price_quote->type_price->value !== 'lista';
        if (!self::storeOrderProduct($request, $order->id, $contado_deb, $redondear)) {
            throw new \Exception('No se pudieron guardar los productos del pedido cliente');
        }

        return $order;
    }

    public function showPedidoOnline($id)
    {
        $order = PedidoOnline::findOrFail($id);
        return sendResponse(new OrderResource($order, 'complete'));
    }

    public function getReportePedidosOnline()
    {
        $orders = Order::where('type_id', 6)->get();
        $orders = OrderResource::collection($orders)->resolve();

        $reporte = array_reduce($orders, function ($carry, $e) {

            if ($e['estado_general'] == 'retirar') {
                $carry->retirar++;
            }
            if ($e['estado_general'] == 'pendiente') {
                $carry->pendiente++;
            }
            if ($e['estado_general'] == 'cancelado') {
                $carry->cancelado++;
            }

            return $carry;
        }, (object) ['retirar' => 0, 'pendiente' => 0, 'cancelado' => 0]);

        return sendResponse($reporte);
    }

    public function updateStateOnline(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $detail = OrderProduct::where('order_id', $id)->get();

            $entregado = Table::where('name', 'order_online_state')->where('value', 'entregado')->first();
            $cacelado = Table::where('name', 'order_online_state')->where('value', 'cancelado')->first();
            foreach ($detail as $item) {
                /* Verificamos que cada item no tenga el estado de entregado o cancelado */
                if ($item->state_id != $entregado->id && $item->state_id != $cacelado->id) {
                    $item->state_id = (int)$request->id;
                    $item->save();
                }
            }

            $order = PedidoOnline::find($id);

            DB::commit();

            return sendResponse(new OrderResource($order, 'complete'));
        } catch (\Exception $e) {
            DB::rollBack();

            return sendResponse(null, $e->getMessage(), 300, $request->all());
        }
    }
}
