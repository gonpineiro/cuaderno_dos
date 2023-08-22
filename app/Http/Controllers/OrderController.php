<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\StoreClienteOrderRequest;
use App\Http\Requests\Order\StoreEnvioOrderRequest;
use App\Http\Requests\Order\StoreOnlineOrderRequest;
use App\Http\Requests\Order\StoreSiniestroOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\Order\OrderProductResource;
use App\Mail\CrearPedidoClienteEmail;
use App\Models\Envio;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\PedidoCliente;
use App\Models\PedidoOnline;
use App\Models\Siniestro;
use App\Models\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderController extends \App\Http\Controllers\Controller
{
    public function indexOnlines(): \Illuminate\Http\JsonResponse
    {

        $order = OrderResource::collection(PedidoOnline::where('type_id', 6)->get());

        return sendResponse($order);
    }

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

    public function indexSiniestros(): \Illuminate\Http\JsonResponse
    {
        $order = OrderResource::collection(Siniestro::where('type_id', 8)->get());

        return sendResponse($order);
    }

    public static function saveOnlineOrder(StoreOnlineOrderRequest $request)
    {
        $user = auth()->user();

        $data = $request->all();
        $data['user_id'] = $user->id;

        $order = PedidoOnline::create($data);

        /* Intentamos guardar lss ordernes productos */
        if (!self::storeOrderProduct($request, $order->id)) {
            DB::rollBack();
            throw new \Exception('No se pudieron guardar los productos del pedido online');
        }

        return $order;
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

    private static function storeOrderProduct($request, $order_id)
    {
        $detail = $request->detail;

        /* if ($this->hayDuplicados($detail)) {
            throw new \Exception("Existen productos duplicados");
        } */

        foreach ($detail as $item) {

            $item['order_id'] = $order_id;
            $item['state_id'] = $item['state']['id'];

            $item['product_id'] = $item['product']['id'];

            if (!OrderProduct::create($item)) {
                throw new \Exception("No se pudo crear un detalle de la orden");
            }
        }
        return true;
    }

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

    public function showPedidoCliente($id)
    {
        $order = PedidoCliente::findOrFail($id);
        return sendResponse(new OrderResource($order, 'complete'));
    }

    public function showPedidoOnline($id)
    {
        $order = PedidoOnline::findOrFail($id);
        return sendResponse(new OrderResource($order, 'complete'));
    }

    public function showSiniestro($id)
    {
        $order = Siniestro::findOrFail($id);
        return sendResponse(new OrderResource($order, 'complete'));
    }

    public function update(UpdateOrderRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $data = $request->all();

            $order = Order::findOrFail($id);

            $order->fill($data)->save();

            /* CORREGIR; NO HAY QUE BORRAR; HAY QUE ACTUALIZAR */
            OrderProduct::where('order_id', $id)->delete();

            /* Intentamos guardar lss ordernes productos */
            if (!$this->storeOrderProduct($request, $order->id)) {
                DB::rollBack();

                return response()->json([
                    'data' => null,
                    'message' => null,
                    'error' => 'No se pudieron guardar los productos de la orden'
                ]);
            }

            DB::commit();

            return sendResponse(new OrderResource($order, 'complete'));
        } catch (\Exception $e) {
            DB::rollBack();

            return sendResponse(null, $e->getMessage(), 300, $request->all());
        }
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
                    $item->state_id = (int)$request->value;
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
                    $item->state_id = (int)$request->value;
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
                    $item->state_id = (int)$request->value;
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
        $correo = new CrearPedidoClienteEmail();;

        // Opcionalmente, puedes agregar lógica adicional después de enviar el correo

        // Redireccionar a una página de éxito, por ejemplo
        return sendResponse(Mail::to('gon.pineiro@gmail.com')->send($correo));
    }

    public function getPdfPedido($id)
    {
        $order = Order::find($id);
        $order->client;
        $detail = OrderProductResource::collection($order->detail);

        //return view('pdf.template', ['pedido' => $order, 'detail' => $detail]);
        $pdf = Pdf::loadView('pdf.pedido', ['pedido' => $order, 'detail' => $detail]);

        return $pdf->download('informe.pdf');
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
}
