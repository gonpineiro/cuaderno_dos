<?php

namespace App\Http\Controllers;

use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\Order\OrderProductResource;
use App\Http\TraitsControllers\TraitPedidos;
use App\Http\TraitsControllers\TraitPedidosSiniestro;
use App\Http\TraitsControllers\TraitPedidosCliente;
use App\Http\TraitsControllers\TraitPedidosOnline;
use App\Http\TraitsControllers\TraitPedidosEmail;
use App\Mail\CrearPedidoClienteEmail;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Shipment;
use App\Models\Table;
use App\Models\ToAsk;
use App\Models\User;
use App\Services\JazzServices\ApiService;
use App\Services\JazzServices\PedidoService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use Spatie\Permission\PermissionRegistrar;

class OrderController extends \App\Http\Controllers\Controller
{
    use TraitPedidosOnline/* Ver si no se usa mas */,
        TraitPedidosCliente/* Ver si no se usa mas */,
        TraitPedidos,
        TraitPedidosSiniestro,
        TraitPedidosEmail;

    private function hayDuplicados($productos)
    {
        // Agrupa los productos por su campo "id"
        $productos_agrupados = collect($productos)->groupBy('product_id');

        // Filtra los grupos que tengan mÃ¡s de un elemento
        $productos_sin_repetidos = $productos_agrupados->filter(function ($grupo) {
            return count($grupo) == 1;
        })->flatten(1)->values()->all();

        // Verifica si hay elementos repetidos y muestra un mensaje de error
        if (count($productos_sin_repetidos) != count($productos)) {
            return true;
        }

        return false;
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $order = Order::findOrFail($id);

            $detail = $request->detail;

            // ObtÃ©n los IDs de producto de detail
            $productIdsInDetail = array_map(function ($item) {
                return $item['product']['id'];
            }, $detail);

            // Elimina los registros OrderProduct que no estÃ¡n en $productIdsInDetail
            OrderProduct::where('order_id', $id)
                ->whereNotIn('product_id', $productIdsInDetail)
                ->delete();

            // Actualiza o agrega registros OrderProduct segÃºn detail
            foreach ($detail as $item) {
                $product = Product::findOrFail($item['product']['id']);
                $unitPrice = $item['unit_price'];

                // Los pedidos con precio distinto de lista mantienen el precio unitario redondeado,
                // igual que cuando se crean desde una cotización.
                if ($order->price_quote->type_price->value !== 'lista' && $product->code !== 'AJUSTE') {
                    $unitPrice = redondearNumero($unitPrice);
                }

                $orderProductData = [
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'provider_id' => !empty($item['provider']) ? $item['provider']['id'] : null,
                    'amount' => $item['amount'],
                    'unit_price' => $unitPrice,
                    /* 'description' => $item['description'], */
                    'state_id' => $item['state']['id'],
                ];

                OrderProduct::updateOrInsert(
                    [
                        'order_id' => $order->id,
                        'product_id' => $item['product']['id'],
                    ],
                    $orderProductData
                );
            }

            DB::commit();
            return sendResponse(new OrderResource($order, 'complete'));
        } catch (\Exception $e) {
            DB::rollBack();

            return sendResponse(null, $e->getMessage(), 300, $request->all());
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $user = User::find(auth()->user()->id);
        if (!$user->can('pedido.delete')) {
            return sendResponse(null, "AcciÃ³n no autorizada");
        }

        try {
            $order = Order::findOrFail($request->id);
            $entregado = OrderProduct::where('order_id', $request->id)->where('state_id', 11)->exists();

            if ($entregado) {
                throw new \Exception("No se puede eliminar un pedido con un producto entregado");
            }

            $order->delete();
            OrderProduct::where('order_id', $request->id)->delete();

            DB::commit();

            return sendResponse(new OrderResource($order));
        } catch (\Exception $e) {
            DB::rollBack();
            return sendResponse(null, $e->getMessage(), 300, $request->id);
        }
    }

    public function getPdfPedido($id)
    {
        $order = Order::find($id);
        $order->client;
        $order->user;
        $detail = OrderProductResource::pdfArray($order->detail_);

        $total = get_total_price($detail);

        $a =  $order->payment_method->value;
        $fecha = \Carbon\Carbon::parse($order->created_at)->format('d/m/Y');
        $data = [
            'pedido' => $order,
            'cotizacion' => $order->price_quote,
            'detail' => OrderProductResource::formatPdf($detail),
            'deposit' => isset($order->deposit) ? formatoMoneda($order->deposit) : null,
            'diferencia' => isset($order->deposit) ? formatoMoneda($total - $order->deposit) : null,
            'total' => formatoMoneda($total),
            'fecha' => $fecha
        ];

        $pdf = Pdf::loadView('pdf.pedido', $data);

        return $pdf->download('informe.pdf');
    }

    private static function storeOrderProduct($request, $order_id, $coef = null, $redondear = false)
    {
        $detail = $request->detail;
        $to_ask = $request->to_ask;

        $noCotizarState = Table::where('name', 'price_quote_state')->where('value', 'no cotizar')->first();
        $noCotizarStateId = $noCotizarState ? $noCotizarState->id : null;

        $detail = array_filter($detail, function ($item) use ($noCotizarStateId) {
            return $noCotizarStateId === null || isset($item['state']['id']) && $item['state']['id'] != $noCotizarStateId;
        });

        $detail = array_values($detail);


        foreach ($detail as $item) {
            $item['order_id'] = $order_id;
            $item['state_id'] = $item['state']['id'];

            $valor = $coef ? $item['unit_price'] * $coef->coeficiente * $coef->value : $item['unit_price'];
            $item['unit_price'] = $redondear ?  redondearNumero($valor) : $valor;

            $item['provider_id'] = isset($item['provider']) ? $item['provider']['id'] : null;
            $item['product_id'] = $item['product']['id'];

            $order_product = OrderProduct::create($item);

            if (!$order_product) {
                throw new \Exception("No se pudo crear el detalle del pedido");
            }

            $toAskElement = array_filter($to_ask, function ($ts) use ($item) {
                return $ts['product_id'] == $item['product']['id'];
            });

            if (!empty($toAskElement)) {
                $toAskElement = array_values($toAskElement);
                $toAsk = ToAsk::create([
                    'order_product_id' => $order_product->id,
                    'product_id' => $toAskElement[0]['product_id'],
                    'provider_id' => $toAskElement[0]['provider_id'],
                    'amount' => $toAskElement[0]['amount'],
                ]);
                if (!$toAsk) {
                    throw new \Exception("No se pudo registro de to_ask");
                }
            }
        }

        if (!empty($detail)) {
            $recargoProducto = Product::productoAjuste();
            $recargos = self::normalizePaymentLines($request);
            $paymentObservationLines = [];

            if ($recargoProducto && !empty($recargos)) {
                foreach ($recargos as $recargoItem) {
                    $medioPago = trim($recargoItem['medio_pago'] ?? 'Medio de pago');
                    $montoBase = isset($recargoItem['monto']) ? (float) $recargoItem['monto'] : 0;
                    $montoRecargo = isset($recargoItem['recargo']) ? (float) $recargoItem['recargo'] : 0;
                    $montoTotal = $montoBase + $montoRecargo;

                    if ($montoTotal > 0) {
                        $montoTotalFormat = number_format($montoTotal, 0, '', '');
                        $paymentObservationLines[] = "{$medioPago}: \${$montoTotalFormat}";
                    }

                    if ($montoRecargo <= 0) {
                        continue;
                    }

                    OrderProduct::create([
                        'product_id' => $recargoProducto->id,
                        'order_id' => $order_id,
                        'state_id' => $detail[0]['state']['id'],
                        'unit_price' => $montoRecargo,
                        'description' => "Financiación {$medioPago}",
                        'amount' => 1,
                    ]);
                }

                self::appendPaymentObservation($order_id, $paymentObservationLines);
            } elseif ($recargoProducto && $request->filled('recargo') && (float) $request->recargo > 0) {
                $data = [
                    'product_id' => $recargoProducto->id,
                    'order_id' => $order_id,
                    'state_id' => $detail[0]['state']['id'],
                    'unit_price' => $request->recargo,
                    'description' => 'Financiación ' . ($request->input('recargo_label') ?: 'Ajuste'),
                    'amount' => 1,
                ];
                OrderProduct::create($data);
            }
        }

        return true;
    }

    private static function normalizePaymentLines(Request $request): array
    {
        $recargos = $request->input('recargos', []);

        if (is_string($recargos)) {
            $decoded = json_decode($recargos, true);
            $recargos = is_array($decoded) ? $decoded : [];
        }

        return is_array($recargos) ? $recargos : [];
    }

    private static function appendPaymentObservation(int $orderId, array $paymentObservationLines): void
    {
        if (empty($paymentObservationLines)) {
            return;
        }

        $order = Order::find($orderId);
        if (!$order) {
            return;
        }

        $observation = (string) $order->observation;
        $parts = explode('@', $observation, 2);
        $baseObservation = trim($parts[0]);
        $paymentObservation = implode(';', $paymentObservationLines);

        $order->observation = $baseObservation . '@' . $paymentObservation;
        $order->save();
    }

    private static function hasPaymentObservation(Order $order): bool
    {
        $parts = explode('@', (string) $order->observation, 2);
        return trim($parts[1] ?? '') !== '';
    }

    private static function hasAdjustmentProduct(Order $order, ?Product $recargoProducto = null): bool
    {
        return $order->detail->contains(function ($detail) use ($recargoProducto) {
            if ($recargoProducto && (int) $detail->product_id === (int) $recargoProducto->id) {
                return true;
            }

            return optional($detail->product)->code === 'AJUSTE';
        });
    }

    private static function ensurePaymentDataForJazz(Order $order, Request $request): void
    {
        $recargos = self::normalizePaymentLines($request);
        if (empty($recargos)) {
            return;
        }

        $order->loadMissing(['detail.product']);
        $recargoProducto = Product::productoAjuste();
        $paymentObservationLines = [];

        foreach ($recargos as $recargoItem) {
            $medioPago = trim($recargoItem['medio_pago'] ?? 'Medio de pago');
            $montoBase = isset($recargoItem['monto']) ? (float) $recargoItem['monto'] : 0;
            $montoRecargo = isset($recargoItem['recargo']) ? (float) $recargoItem['recargo'] : 0;
            $montoTotal = $montoBase + $montoRecargo;

            if ($montoTotal > 0) {
                $montoTotalFormat = number_format($montoTotal, 0, '', '');
                $paymentObservationLines[] = "{$medioPago}: \${$montoTotalFormat}";
            }
        }

        if (!self::hasPaymentObservation($order) && !empty($paymentObservationLines)) {
            self::appendPaymentObservation($order->id, $paymentObservationLines);
        }

        if (!$recargoProducto || self::hasAdjustmentProduct($order, $recargoProducto)) {
            return;
        }

        $baseDetail = $order->detail->first(function ($detail) use ($recargoProducto) {
            return (int) $detail->product_id !== (int) $recargoProducto->id;
        });

        if (!$baseDetail) {
            return;
        }

        foreach ($recargos as $recargoItem) {
            $montoRecargo = isset($recargoItem['recargo']) ? (float) $recargoItem['recargo'] : 0;
            if ($montoRecargo <= 0) {
                continue;
            }

            $medioPago = trim($recargoItem['medio_pago'] ?? 'Ajuste');

            OrderProduct::create([
                'product_id' => $recargoProducto->id,
                'order_id' => $order->id,
                'state_id' => $baseDetail->state_id,
                'unit_price' => $montoRecargo,
                'description' => "Financiación {$medioPago}",
                'amount' => 1,
            ]);
        }
    }

    private static function getJazzObservation(Order $order): ?string
    {
        $observation = (string) $order->observation;
        $parts = explode('@', $observation, 2);
        $orderObservation = trim($parts[0] ?? '');
        $paymentObservation = trim($parts[1] ?? '');

        $recargo = $order->detail->first(function ($detail) {
            return $detail->product->code === 'AJUSTE';
        });

        if ($paymentObservation === '' && $recargo) {
            $paymentObservation = trim((string) $recargo->description);
        }

        if ($orderObservation !== '' && $paymentObservation !== '') {
            return "Pedido: {$orderObservation} - Medios de pago: {$paymentObservation}";
        }

        if ($orderObservation !== '') {
            return "Pedido: {$orderObservation}";
        }

        if ($paymentObservation !== '') {
            return "Medios de pago: {$paymentObservation}";
        }

        return null;
    }

    public function enviarCorreo()
    {

        $p = Order::find(41);
        //return TraitPedidosEmail::pedidoProductoUnico($p);

        //return TraitPedidosEmail::pedidoRetirar($p);

        $oc = PurchaseOrder::find(1);

        return TraitPedidosEmail::ordenCompra($oc);
        return TraitPedidosEmail::pedidoUnicoRetirar($p);
        $s = Shipment::find(4);
        //return TraitPedidosEmail::envioDespachado($s);
        // return TraitPedidosEmail::pedidoRetirar($p);


        return TraitPedidosEmail::pedidoEntregado($p);
    }

    public function generar_factura_jazz(Request $request)
    {
        $order = Order::where("id", $request->id)->with(['detail.product', 'client'])->first();

        if (!$order) {
            return sendResponse(null, 'No se encuentra el pedido');
        }

        /* Potencialmente hay que retirarlo */
        /* $allHaveIdProducto = $order->detail->every(function ($detail) {
            return !empty($detail->product->idProducto);
        });

        if (!$allHaveIdProducto) {
            return sendResponse(null, 'Hay productos sin relacion con el Jazz', 410);
        } */

        if ($order->ref_jazz_id) {
            return sendResponse(null, "Este pedido ya tiene una relación con Pedidos de jazz. N°: $order->ref_jazz_id", 410);
        }

        try {
            self::ensurePaymentDataForJazz($order, $request);
            $res =  $this->generar_pedido_jazz($order);
            return sendResponse($res);
        } catch (\Exception $e) {
            return sendResponse(null, $e->getMessage(), 303);
        }
    }

    public static function generar_pedido_jazz($order)
    {
        $order = $order->fresh(['detail.product', 'detail.state', 'client', 'price_quote.type_price']);
        $service = new PedidoService();
        $detailSinCancelados = $order->detail->filter(function ($detail) {
            return optional($detail->state)->value !== 'cancelado';
        })->values();

        if ($detailSinCancelados->isEmpty()) {
            throw new \Exception('El pedido no tiene productos activos para enviar a Jazz');
        }

        $order->setRelation('detail', $detailSinCancelados);

        // Detectar productos de recargo (código "AJUSTE")
        $recargo = $order->detail->filter(function ($detail) {
            return $detail->product->code === 'AJUSTE';
        })->first();

        // Generar observación con el recargo si existe
        $observation = self::getJazzObservation($order);
        if (!$observation && $recargo) {
            $observation = $recargo->description;
        }

        $data = $service->getFormatData($order, $order->client->jazz_id, $observation);

        try {
            $id_pedido_jazz = $service->crearPedidoCompleto($data, $order);

            return "Pedido Jazz N°: $id_pedido_jazz generado correctamente!";
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

