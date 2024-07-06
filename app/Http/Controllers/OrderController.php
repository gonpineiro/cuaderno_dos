<?php

namespace App\Http\Controllers;

use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\Order\OrderProductResource;
use App\Http\TraitsControllers\TraitPedidos;
use App\Http\TraitsControllers\TraitPedidosSiniestro;
use App\Http\TraitsControllers\TraitPedidosCliente;
use App\Http\TraitsControllers\TraitPedidosOnline;
use App\Mail\CrearPedidoClienteEmail;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\ToAsk;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderController extends \App\Http\Controllers\Controller
{

    use TraitPedidosOnline, TraitPedidosCliente, TraitPedidos, TraitPedidosSiniestro;

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
                    'description' => $item['description'],
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

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $order = Order::findOrFail($id);
            $entregado = OrderProduct::where('order_id', $id)->where('state_id', 11)->exists();

            if ($entregado) {
                throw new \Exception("No se puede eliminar un pedido con un producto entregado");
            }

            $order->delete();
            OrderProduct::where('order_id', $id)->delete();

            DB::commit();

            return sendResponse($id);
        } catch (\Exception $e) {
            DB::rollBack();

            return sendResponse(null, $e->getMessage(), 300, $id);
        }
    }

    public function enviarCorreo()
    {

        // Enviar el correo electrónico

        // Opcionalmente, puedes agregar lógica adicional después de enviar el correo

        // Redireccionar a una página de éxito, por ejemplo
        return sendResponse(Mail::to('gon.pineiro@gmail.com')->send(new CrearPedidoClienteEmail()));
    }

    public function getPdfPedido($id)
    {
        $order = Order::find($id);
        $order->client;
        $detail = OrderProductResource::pdfArray($order->detail);

        $total = get_total_price($detail);

        $data = [
            'pedido' => $order,
            'cotizacion' => $order->price_quote,
            'detail' => OrderProductResource::formatPdf($detail),
            'total' => formatoMoneda($total),
        ];

        $pdf = Pdf::loadView('pdf.pedido', $data);

        return $pdf->download('informe.pdf');
    }

    private function storeOrderProduct($request, $order_id, $coef = null)
    {
        $detail = $request->detail;
        $to_ask = $request->to_ask;

        foreach ($detail as $item) {
            $item['order_id'] = $order_id;
            $item['state_id'] = $item['state']['id'];
            $item['unit_price'] = redondearNumero($coef ? $item['unit_price'] * $coef->coeficiente * $coef->value : $item['unit_price']);

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
}
