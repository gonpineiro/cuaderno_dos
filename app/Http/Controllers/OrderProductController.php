<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderResource;
use App\Models\Envio;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\PedidoCliente;
use App\Models\Siniestro;
use Illuminate\Http\Request;

class OrderProductController extends Controller
{
    public function updateOnline(Request $request)
    {
        $order_product =
            OrderProduct::where('order_id', $request->order_id)
            ->where('product_id', $request->product_id)->first();

        $update = $order_product->update($request->all());

        if ($update) {
            $order = Order::findOrFail($request->order_id);
            return sendResponse(new OrderResource($order, 'complete'));
        }
        return sendResponse(null, 'Error a modificar el detalle');
    }

    public function updateCliente(Request $request)
    {
        $order_product =
            OrderProduct::where('order_id', $request->order_id)
            ->where('product_id', $request->product_id)->first();

        $update = $order_product->update($request->all());

        if ($update) {
            $order = PedidoCliente::findOrFail($request->order_id);
            return sendResponse(new OrderResource($order, 'complete'));
        }
        return sendResponse(null, 'Error a modificar el detalle');
    }

    public function updateSiniestro(Request $request)
    {
        $order_product =
            OrderProduct::where('order_id', $request->order_id)
            ->where('product_id', $request->product_id)->first();

        $update = $order_product->update($request->all());

        if ($update) {
            $order = Siniestro::findOrFail($request->order_id);
            return sendResponse(new OrderResource($order, 'complete'));
        }
        return sendResponse(null, 'Error a modificar el detalle');
    }
}
