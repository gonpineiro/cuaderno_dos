<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\Order\OrderProductResource;
use App\Mail\MiCorreoMailable;
use App\Models\Order;
use App\Models\OrderProduct;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderController extends \App\Http\Controllers\Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        if ($type = $request->query('type')) {
            $order =  OrderResource::collection(Order::where('type_id', $type)->get());
        } else {
            $order = OrderResource::collection(Order::all());
        }

        return sendResponse($order);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Order\StoreOrderRequest  $request
     * @return \App\Http\Resources\Order\OrderResource|\Illuminate\Http\JsonResponse
     */
    public static function store(StoreOrderRequest $request)
    {
        DB::beginTransaction();

        try {
            $order = self::saveOrder($request);

            DB::commit();

            return sendResponse(new OrderResource($order, 'complete'));
        } catch (\Exception $e) {
            DB::rollBack();

            return sendResponse(null, $e->getMessage(), 300, $request->all());
        }
    }

    public static function saveOrder(StoreOrderRequest $request)
    {
        $user = auth()->user();

        $data = $request->all();
        $data['user_id'] = $user->id;

        $order = Order::create($data);

        /* Intentamos guardar lss ordernes productos */
        if (!self::storeOrderProduct($request, $order->id)) {
            DB::rollBack();
            throw new \Exception('No se pudieron guardar los productos de la orden');
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $order = Order::findOrFail($id);
        return sendResponse(new OrderResource($order, 'complete'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Order\UpdateOrderRequest $request
     * @param  \App\Models\Order $order
     * @return \Illuminate\Http\JsonResponse
     */
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

    public function updateState(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $detail = OrderProduct::where('order_id', $id)->get();

            foreach ($detail as $item) {
                /* Verificamos que cada item no tenga el estado de entregado o cancelado */
                if ($item->state_id != 11 && $item->state_id != 12) {
                    $item->state_id = (int)$request->value;
                    $item->save();
                }
            }

            $order = Order::find($id);

            DB::commit();

            return sendResponse(new OrderResource($order, 'complete'));
        } catch (\Exception $e) {
            DB::rollBack();

            return sendResponse(null, $e->getMessage(), 300, $request->all());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\JsonResponse
     */
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
        $correo = new MiCorreoMailable();
        Mail::to('destinatario@example.com')->send($correo);

        // Opcionalmente, puedes agregar lógica adicional después de enviar el correo

        // Redireccionar a una página de éxito, por ejemplo
        return redirect()->route('correo.enviado');
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
