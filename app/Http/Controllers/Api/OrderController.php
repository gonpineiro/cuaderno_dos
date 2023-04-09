<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Api\Order;
use App\Models\Api\OrderProduct;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
     * @return \App\Http\Resources\OrderResource|\Illuminate\Http\JsonResponse
     */
    public function store(StoreOrderRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = auth()->user();

            $data = $request->all();
            $data['user_id'] = $user->id;

            $order = Order::create($data);

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

            return sendResponse(new OrderResource($order));
        } catch (Exception $e) {
            DB::rollBack();

            return sendResponse(null, $e->getMessage(), 300, $request->all());
        }
    }
    private function storeOrderProduct($request, $order_id)
    {
        $orders_products = $request->orders_products;

        if ($this->hayDuplicados($orders_products)) {
            throw new Exception("Existen productos duplicados");
        }

        foreach ($orders_products as $order_product) {

            $order_product['order_id'] = $order_id;

            /* Cuando es un order_producto del catalogo */
            if (isset($order_product['isProduct']) && $order_product['isProduct']) {
                $order_product['product_id'] = (int) $order_product['id'];
            }

            /* Cuando no es un order_producto del catalogo */
            if (isset($order_product['isOtherProduct']) && $order_product['isOtherProduct']) {
                $order_product['other_id'] = (int) $order_product['id'];
            }

            unset($order_product['id']);

            if (!OrderProduct::create($order_product)) {
                throw new Exception("No se pudo crear un detalle de la orden");
            }
        }
        return true;
    }

    private function hayDuplicados($productos)
    {
        // Agrupa los productos por su campo "id"
        $productos_agrupados = collect($productos)->groupBy('id');

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
     * @param  \App\Models\Api\Order  $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $order = Order::findOrFail($id);
        return sendResponse(new OrderResource($order));
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
