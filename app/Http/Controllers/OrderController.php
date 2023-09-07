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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderController extends \App\Http\Controllers\Controller
{

    use TraitPedidosOnline, TraitPedidosCliente, TraitPedidos, TraitPedidosSiniestro;

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

    public function update(Request $request, $id)
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
}
