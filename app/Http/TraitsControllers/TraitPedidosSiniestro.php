<?php

namespace App\Http\TraitsControllers;

use App\Http\Requests\Order\StoreClienteOrderRequest;
use App\Http\Requests\Order\StoreSiniestroOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Models\OrderProduct;
use App\Models\PedidoCliente;
use App\Models\Siniestro;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait TraitPedidosSiniestro
{
    public function indexSiniestros(): \Illuminate\Http\JsonResponse
    {
        $order = OrderResource::collection(Siniestro::where('type_id', 8)->get());

        return sendResponse($order);
    }

    public static function saveSiniestroOrder(StoreSiniestroOrderRequest $request)
    {
        $user = auth()->user();

        $data = $request->all();
        $data['user_id'] = $user->id;

        $order = Siniestro::create($data);

        /* Intentamos guardar lss ordernes productos */
        if (!self::storeOrderProduct($request, $order->id)) {
            DB::rollBack();
            throw new \Exception('No se pudieron guardar los productos del siniestro');
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
