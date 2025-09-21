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
use App\Models\PurchaseOrder;
use App\Models\Shipment;
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

        // Filtra los grupos que tengan más de un elemento
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

            // Obtén los IDs de producto de detail
            $productIdsInDetail = array_map(function ($item) {
                return $item['product']['id'];
            }, $detail);

            // Elimina los registros OrderProduct que no están en $productIdsInDetail
            OrderProduct::where('order_id', $id)
                ->whereNotIn('product_id', $productIdsInDetail)
                ->delete();

            // Actualiza o agrega registros OrderProduct según detail
            foreach ($detail as $item) {
                $orderProductData = [
                    'order_id' => $order->id,
                    'product_id' => $item['product']['id'],
                    'provider_id' => $item['provider'] ? $item['provider']['id'] : null,
                    'amount' => $item['amount'],
                    'unit_price' => $item['unit_price'],
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
            return sendResponse(null, "Acción no autorizada");
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

    private function storeOrderProduct($request, $order_id, $coef = null, $redondear = false)
    {
        $detail = $request->detail;
        $to_ask = $request->to_ask;

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


            /* if (!!$item['product']['is_special']) {

            } */
        }
        return true;
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

        $allHaveIdProducto = $order->detail->every(function ($detail) {
            return !empty($detail->product->idProducto);
        });

        if (!$allHaveIdProducto) {
            return sendResponse(null, 'Hay productos sin relacion con el Jazz', 410);
        }

        $service = new PedidoService();
        $data = [
            "empresa" => 2,
            "sucursal" => 2,
            "letra" => "B",
            "boca" => 0,
            "idCliente" => 15211,
            "ivaTipo" => 0,
            "idVendedor" => 1,
            "vendedorComision" => 0,
            "idLista" => 2, //
            "obs" => "SALDO INICIAL",
            "condicion" => 2,
            "moneda" => 1,
            "enMostrador" => "N",
            "fecha" => \Carbon\Carbon::now('UTC')->format('Y-m-d\TH:i:s.v\Z'),
            "descuento" => "string",
            "recargo" => "string",
            "idEstado" => 0
        ];
        $pedido = $service->agregarPedido($data);

        if (!isset($pedido['refID']) || !$pedido['refID']) {
            return sendResponse(null, 'No se logro crear el pedido', 303, $pedido);
        }

        $productData = $order->getJazzData($pedido['refID']);

        foreach ($productData as $data) {
            $pedido_producto = $service->agregarArticulo($data);
            $a = 'ocurre error....';
        }

        $order->ref_jazz_id = $pedido['refID'];
        $order->save();

        return $order;
    }
}
