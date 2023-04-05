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
        $data = $request->all();
        DB::beginTransaction();

        try {
            $user = auth()->user();

            $order = Order::create([
                'user_id' => $user->id,
                'description' => '$request->description',
                'type_id' => $request->type_id,
                'client_id' => $request->client_id,
            ]);

            if (!$this->storeOrderProduct($request, $order->id)) {
                DB::rollBack();

                return response()->json([
                    'data' => null,
                    'message' => null,
                    'error' => 'No se pudieron guardar los productos de la orden'
                ]);
            }

            DB::commit();

            return new OrderResource($order);
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

        foreach ($orders_products as $product) {
            $product = (object) $product;

            $array = [
                'order_id' => $order_id,
                'state_id' => $product->state_id,
                'amount' => $product->amount,
                'unit_price' => $product->amount,
                'detalle' => $product->detalle,
            ];

            /* Cuando es un producto del catalogo */
            if (isset($product->isProduct) && $product->isProduct) {
                $array['product_id'] = (int) $product->id;
            }

            /* Cuando no es un producto del catalogo */
            if (isset($product->isOtherProduct) && $product->isOtherProduct) {
                $array['other_id'] = (int) $product->id;
            }

            unset($array['id']);

            if (!OrderProduct::create($array)) {
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
