<?php

namespace App\Http\TraitsControllers;

use App\Http\Requests\Order\StoreSiniestroOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Models\Coeficiente;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Siniestro;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait TraitPedidosSiniestro
{
    public function indexSiniestros(): \Illuminate\Http\JsonResponse
    {
        $siniestro = Table::where('name', 'order_type')->where('value', 'siniestro')->first();
        $order = OrderResource::collection(Siniestro::where('type_id', $siniestro->id)->get());

        return sendResponse($order);
    }

    public static function saveSiniestroOrder(StoreSiniestroOrderRequest $request)
    {
        $user = auth()->user();

        $data = $request->all();
        $data['user_id'] = $user->id;

        $order = Siniestro::create($data);

        $is_contado = $order->price_quote->type_price->value == 'contado';
        $contado_deb = $is_contado ? Coeficiente::find(2) : null;

        /* Intentamos guardar lss ordernes productos */
        $redondear = $order->price_quote->type_price->value !== 'lista';
        if (!self::storeOrderProduct($request, $order->id, $contado_deb, $redondear)) {
            throw new \Exception('No se pudieron guardar los productos del pedido cliente');
        }

        return $order;
    }

    public function showSiniestro($id)
    {
        $order = Siniestro::findOrFail($id);
        return sendResponse(new OrderResource($order, 'complete'));
    }

    public function updateStateSiniestro(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $order = Order::find($request->order_id);

            if ($order->shipment) {
                return sendResponse(null, 'Ya existe un envio creado con este pedido', 300);
            }

            $detail = OrderProduct::where('order_id', $id)->get();

            $entregado = Table::where('name', 'order_siniestro_state')->where('value', 'entregado')->first();
            $cacelado = Table::where('name', 'order_siniestro_state')->where('value', 'cancelado')->first();
            foreach ($detail as $item) {
                /* Verificamos que cada item no tenga el estado de entregado o cancelado */
                if ($item->state_id != $entregado->id && $item->state_id != $cacelado->id) {
                    $item->state_id = (int)$request->id;
                    $item->save();
                }
            }

            $order = Siniestro::find($id);

            DB::commit();

            return sendResponse(new OrderResource($order, 'complete'));
        } catch (\Exception $e) {
            DB::rollBack();

            return sendResponse(null, $e->getMessage(), 300, $request->all());
        }
    }
}
