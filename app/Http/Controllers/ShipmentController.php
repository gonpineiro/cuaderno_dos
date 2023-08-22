<?php

namespace App\Http\Controllers;

use App\Http\Requests\Shipment\StoreShipmentRequest;
use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\Shipment\ShipmentResource;
use App\Models\Order;
use App\Models\PedidoCliente;
use App\Models\PedidoOnline;
use App\Models\Shipment;
use App\Models\ShipmentProduct;
use App\Models\Siniestro;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShipmentController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $shipment = Shipment::all();

        return sendResponse(ShipmentResource::collection($shipment));
    }

    public function store(StoreShipmentRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = auth()->user();

            $data = $request->all();
            $data['user_id'] = $user->id;

            $order = Order::find($request->order_id);
            if ($order->shipment_id) {
                throw new \Exception('El pedido ya tiene un envío asigando');
            }

            $shipment = Shipment::create($data);

            if (!self::storeShipmentProduct($request, $shipment->id)) {
                DB::rollBack();
                throw new \Exception('No se pudieron guardar los productos del pedido cliente');
            }

            $order = Order::find($request->order_id);
            $order->shipment_id = $shipment->id;
            $order->save();

            $order_type = Table::find($order->type_id);

            if ($order_type->value === 'online') {
                $order = PedidoOnline::findOrFail($order->id);
            } else if ($order_type->value === 'cliente') {
                $order = PedidoCliente::findOrFail($order->id);
            } else if ($order_type->value === 'siniestro') {
                $order = Siniestro::findOrFail($order->id);
            }

            DB::commit();

            return sendResponse([
                'envio' => new ShipmentResource($shipment, 'complete'),
                'pedido' => new OrderResource($order),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return sendResponse(null, $e->getMessage(), 300, $request->all());
        }
    }

    private static function storeShipmentProduct($request, $shipment_id)
    {
        $detail = $request->detail;

        /* if ($this->hayDuplicados($detail)) {
            throw new \Exception("Existen productos duplicados");
        } */

        $pendiente = Table::where('name', 'order_envio_state')->where('value', 'pendiente')->first();
        foreach ($detail as $item) {

            $item['shipment_id'] = $shipment_id;
            $item['state_id'] = $pendiente->id;

            $item['product_id'] = $item['product']['id'];

            if (!ShipmentProduct::create($item)) {
                throw new \Exception("No se pudo crear un detalle de la orden");
            }
        }
        return true;
    }

    public function updateState(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $detail = ShipmentProduct::where('shipment_id', $id)->get();

            $cacelado = Table::where('name', 'order_envio_state')->where('value', 'cancelado')->first();
            foreach ($detail as $item) {
                /* Verificamos que cada item no tenga el estado de entregado */
                if ($item->state_id != $cacelado->id) {
                    $item->state_id = (int)$request->value;
                    $item->save();
                }
            }

            $shipment = Shipment::find($id);

            DB::commit();

            return sendResponse(new ShipmentResource($shipment, 'complete'));
        } catch (\Exception $e) {
            DB::rollBack();

            return sendResponse(null, $e->getMessage(), 300, $request->all());
        }
    }

    public function updateEnvio(Request $request)
    {
        $shipment_product =
            ShipmentProduct::where('shipment_id', $request->shipment_id)
            ->where('product_id', $request->product_id)->first();

        $update = $shipment_product->update($request->all());

        if ($update) {
            $shipment = Shipment::findOrFail($request->shipment_id);
            return sendResponse(new ShipmentResource($shipment, 'complete'));
        }
        return sendResponse(null, 'Error a modificar el detalle');
    }

    public function show($id)
    {
        $shipment = Shipment::findOrFail($id);
        return sendResponse(new ShipmentResource($shipment, 'complete'));
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $shipment = Shipment::findOrFail($id);
            $shipment->delete();

            DB::commit();

            return sendResponse($id);
        } catch (\Exception $e) {
            DB::rollBack();

            return sendResponse(null, $e->getMessage(), 300, $id);
        }
    }
}
