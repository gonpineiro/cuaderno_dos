<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Http\Request;

class OrderProductController extends Controller
{
    public function update(Request $request)
    {
        $order_product =
            OrderProduct::where('order_id', $request->order_id)
            ->where('product_id', $request->product_id)
            ->where('other_id', $request->other_id)->first();

        $update = $order_product->update($request->all());

        if ($update) {
            $order = Order::findOrFail($request->order_id);
            return sendResponse(new OrderResource($order));
        }
        return sendResponse(null, 'Error a modificar el detalle');
    }
}
