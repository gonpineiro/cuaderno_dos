<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

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

class ShipmentController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $shipments = Shipment::orderBy('created_at')->get();

        $shipments = $shipments->sortBy(function ($shipment) {
            return [
                'pendiente' => 1,
                'listo_enviar' => 2,
                'despachado' => 3,
                'contrareemboldo' => 4,
            ][$shipment->getGeneralState()->value];
        });

        return sendResponse(ShipmentResource::collection($shipments));
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

            $type = $order->type->value;
            $newDetail = $order->detailPending;

            self::changeToRetirar($type, $newDetail);

            $shipment = Shipment::create($data);

            if (!self::storeShipmentProduct($newDetail, $shipment->id)) {
                DB::rollBack();
                throw new \Exception('No se pudieron guardar los productos del pedido cliente');
            }

            $order->shipment_id = $shipment->id;
            $order->save();

            if ($type === 'online') {
                $order = PedidoOnline::findOrFail($order->id);
            } else if ($type === 'cliente') {
                $order = PedidoCliente::findOrFail($order->id);
            } else if ($type === 'siniestro') {
                $order = Siniestro::findOrFail($order->id);
            }

            DB::commit();

            return sendResponse([
                'envio' => new ShipmentResource($shipment, 'complete'),
                'pedido' => new OrderResource($order, 'complete'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return sendResponse(null, $e->getMessage(), 300, $request->all());
        }
    }

    private static function changeToRetirar($order_type, $detail)
    {
        if ($order_type == 'online') {
            $state = Table::where('name', 'order_online_state')->where('value', 'retirar')->first();
        } else if ($order_type == 'cliente') {
            $state = Table::where('name', 'order_cliente_state')->where('value', 'retirar')->first();
        } else if ($order_type == 'siniestro') {
            $state = Table::where('name', 'order_siniestro_state')->where('value', 'completo')->first();
        }

        foreach ($detail as $orderProduct) {
            $orderProduct->state_id = $state->id;
            $orderProduct->save();
        }
    }

    private static function storeShipmentProduct($detail, $shipment_id)
    {
        $pendiente = Table::where('name', 'order_envio_state')->where('value', 'pendiente')->first();

        foreach ($detail as $item) {

            $data = [];
            $data['shipment_id'] = $shipment_id;
            $data['state_id'] = $pendiente->id;
            $data['product_id'] = $item['product']['id'];
            $data['amount'] = $item['amount'];
            $data['unit_price'] = $item['unit_price'];
            $data['provider_id'] = isset($item['provider']) ? $item['provider']['id'] : null;

            if (!ShipmentProduct::create($data)) {
                throw new \Exception("No se pudo crear un detalle de la orden");
            }
        }
        return true;
    }

    public static function storeShipment($envio, $order)
    {
        $shipment = Shipment::create([
            'user_id' => auth()->user()->id,
            'order_id' => $order->id,
            'client_id' => $order->client_id,
            'payment_method_id' => $envio['payment_method_id'],
            'transport' => isset($envio['transport']) ? $envio['transport'] : null,
            'invoice_number' => isset($envio['invoice_number']) ? $envio['invoice_number'] : null,
            'nro_guia' => isset($envio["nro_guia"]) ? $envio["nro_guia"] : null,
            'bultos' => $envio['bultos'],
            'send_adress' => $envio['send_adress'],
        ]);

        self::storeShipmentProduct($order->detail, $shipment->id);

        return $shipment;
    }

    public function updateState(Request $request)
    {
        DB::beginTransaction();

        try {
            $detail = ShipmentProduct::where('shipment_id', $request->shipment_id)->get();

            $cacelado = Table::where('name', 'order_envio_state')->where('value', 'cancelado')->first();
            foreach ($detail as $item) {
                /* Verificamos que cada item no tenga el estado de entregado */
                if ($item->state_id != $cacelado->id) {
                    $item->state_id = (int)$request->state_id;
                    $item->save();
                }
            }

            $shipment = Shipment::find($request->shipment_id);

            DB::commit();

            return sendResponse(new ShipmentResource($shipment, 'complete'));
        } catch (\Exception $e) {
            DB::rollBack();

            return sendResponse(null, $e->getMessage(), 300, $request->all());
        }
    }

    public function update_envio_product(Request $request)
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

    public function get_pdf(Request $request, $id)
    {
        $shipment = Shipment::find($id);
        $shipment->client;

        $detail = ShipmentResource::pdfArray($shipment->detail);

        $total = get_total_price($detail);

        $vars = [
            'cotizacion' => $shipment,
            'detail' => ShipmentResource::formatPdf($detail),
            'total' => formatoMoneda($total),
            'type' => $request->type,
        ];

        $pdf = Pdf::loadView("pdf.envios.$request->type", $vars);

        return $pdf->download('informe.pdf');
    }

    public function show(Request $requets, $id)
    {
        $shipment = Shipment::findOrFail($id);
        if ($requets->type) {
            $method = $requets->type;
            return sendResponse(ShipmentResource::$method($shipment));
        }
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

    public function updateEnvio(Request $request, int $id)
    {
        $shipment = Shipment::findOrFail($id);

        $shipment->fill($request->all())->save();

        return sendResponse(new ShipmentResource($shipment, 'complete'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $shipment = Shipment::findOrFail($id);

            $detail = $request->detail;

            // Obtén los IDs de producto de detail
            $productIdsInDetail = array_map(function ($item) {
                return $item['product']['id'];
            }, $detail);

            // Elimina los registros OrderProduct que no están en $productIdsInDetail
            ShipmentProduct::where('shipment_id', $id)
                ->whereNotIn('product_id', $productIdsInDetail)
                ->delete();

            // Actualiza o agrega registros OrderProduct según detail
            foreach ($detail as $item) {
                $shipmentProductData = [
                    'shipment_id' => $shipment->id,
                    'product_id' => $item['product']['id'],
                    'amount' => $item['amount'],
                    'unit_price' => $item['unit_price'],
                    /* 'description' => $item['description'], */
                    'state_id' => $item['state']['id'],
                    'provider_id' => isset($item['provider']) ? $item['provider']['id'] : null,
                ];

                ShipmentProduct::updateOrInsert(
                    [
                        'shipment_id' => $shipment->id,
                        'product_id' => $item['product']['id'],
                    ],
                    $shipmentProductData
                );
            }

            DB::commit();
            return sendResponse(new ShipmentResource($shipment, 'complete'));
        } catch (\Exception $e) {
            DB::rollBack();

            return sendResponse(null, $e->getMessage(), 300, $request->all());
        }
    }
}
