<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Api\Order;
use App\Models\Api\OrderProduct;

class OrderController extends \App\Http\Controllers\Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return OrderResource::collection(Order::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Order\StoreOrderRequest  $request
     * @return \App\Http\Resources\OrderResource|\Illuminate\Http\JsonResponse
     */
    public function store(StoreOrderRequest $request)
    {
        $order = Order::create(['detalle' => $request->get('detalle')]);
        $products_id = $request->all()['products'];

        if (!$this->storeOrderProduct($products_id, $order->id)) {

            $order = Order::findOrFail($order->id);
            $order->delete();

            return response()->json([
                'data' => null,
                'message' => null,
                'error' => 'No se pudieron guardar los '
            ]);
        }

        return new OrderResource($order);
    }

    private function storeOrderProduct($products_id, $order_id)
    {
        $products_id = explode(',', $products_id);

        /* Verificamos elementos duplicados */
        if (count(array_unique($products_id)) < count($products_id)) {
            return false;
        }

        $orders = array_map(function ($product_id) use ($order_id) {
            return ['order_id' => $order_id, 'product_id' => (int)$product_id];
        }, $products_id);

        return OrderProduct::insert($orders);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Api\Order  $order
     * @return \App\Http\Resources\OrderResource
     */
    public function show($id)
    {
        $order = Order::findOrFail($id);
        return new OrderResource($order);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Order\UpdateOrderRequest  $request
     * @param  \App\Models\Api\Order  $order
     * @return \App\Http\Resources\OrderResource
     */
    public function update(UpdateOrderRequest $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->fill($request->all())->save();
        return new OrderResource($order);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Api\Order  $order
     * @return \App\Http\Resources\OrderResource
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return new OrderResource($order);
    }
}
