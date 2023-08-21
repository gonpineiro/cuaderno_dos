<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\StoreProductOutRequest;
use App\Models\Product;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\StoreProductSpecialRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\PriceQuote\PriceQuoteResource;
use App\Http\Resources\ProductResource;
use App\Models\Order;
use App\Models\PriceQuoteProduct;
use App\Models\OrderProduct;
use Illuminate\Http\Request;

class ProductController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $products = ProductResource::collection(Product::all());
        return sendResponse($products);
    }

    public function relation(Request $request)
    {
        $model = $request->model;
        $state_id = $request->state_id;

        $products = Product::withCount([
            /* Cantidad de padres (cotizacion o pedidos) */
            "$model as count" => function ($query) use ($state_id) {
                $query->where('state_id', $state_id);
            }
        ])
            /* Suma total de la cantidad de productos en todos los padres (cotizacion o pedidos) */
            ->withSum(["$model as sum_amount" => function ($query) use ($state_id) {
                $query->where('state_id', $state_id);
            }], 'amount')

            /* Opcional: para asegurarse de que haya al menos una relación con PriceQuoteProduct en el estado filtrado */
            ->having('count', '>', 0)
            ->orderBy('sum_amount', 'desc')
            ->get();

        return sendResponse(ProductResource::collection($products));
    }

    public function relationEmptyStock(Request $request)
    {
        $model = $request->model;
        $state_id = $request->state_id;

        $products = Product::where('min_stock', true)->withCount([
            /* Cantidad de padres (cotizacion o pedidos) */
            "$model as count" => function ($query) use ($state_id) {
                $query->where('state_id', $state_id);
            }
        ])
            /* Suma total de la cantidad de productos en todos los padres (cotizacion o pedidos) */
            ->withSum(["$model as sum_amount" => function ($query) use ($state_id) {
                $query->where('state_id', $state_id);
            }], 'amount')

            /* Opcional: para asegurarse de que haya al menos una relación con PriceQuoteProduct en el estado filtrado */
            ->having('count', '>', 0)
            ->orderBy('sum_amount', 'desc')
            ->get();

        return sendResponse(ProductResource::collection($products));
    }

    public function cotizaciones(Request $request)
    {
        try {
            $code = $request->id;
            $product = Product::where('code', $code)->first();

            $pq = PriceQuoteProduct::where('product_id', $product->id)
                ->where('state_id', 27)->with('price_quote')->get();

            $priceQuotes = $pq->map(function ($item) {
                return $item->price_quote;
            });

            $priceQuotes = PriceQuoteResource::collection($priceQuotes);
            return sendResponse($priceQuotes);
        } catch (\Exception $e) {
            return sendResponse(null, $e->getMessage(), 300);
        }
    }

    public function pedidos(Request $request)
    {
        try {
            $code = $request->id;
            $model = $request->model;

            if ($model == 'pedidos_online') {
                $state_id = 10;
            } else if ($model == 'pedidos_cliente') {
                $state_id = 14;
            } else if ($model == 'pedidos_siniestro') {
                $state_id = 17;
            } else if ($model == 'cotizaciones') {
                return $this->cotizaciones($request);
            }
            $product = Product::where('code', $code)->first();

            $pq = OrderProduct::where('product_id', $product->id)
                ->where('state_id', $state_id)->with('order')->get();

            $orders = $pq->map(function ($item) {
                $o = $item->order;
                return $item->order;
            });

            $orders = OrderResource::collection($orders);
            return sendResponse($orders);
        } catch (\Exception $e) {
            return sendResponse(null, $e->getMessage(), 300);
        }
    }

    /* public function inPedidoOnline()
    {
        $products = Product::where('empty_stock', true)
            ->withCount([
                'orderProduct as count_order' => function ($query) {
                    $query->where('state_id', 9);
                }
            ])
            ->withSum(['orderProduct as sum_amount' => function ($query) {
                $query->where('state_id', 9);
            }], 'amount')

            ->having('count_order', '>', 0)
            ->orderBy('count_order', 'desc')
            ->get();

        return sendResponse($products);
    } */

    public function store(StoreProductRequest $request)
    {
        try {
            $product = Product::create($request->all());
            return sendResponse(new ProductResource($product));
        } catch (\Exception $e) {
            return sendResponse(null, $e->getMessage());
        }
    }

    public function storeOutCatalogue(StoreProductOutRequest $request)
    {
        try {
            $product = Product::create($request->all());
            return sendResponse(new ProductResource($product));
        } catch (\Exception $e) {
            return sendResponse(null, $e->getMessage());
        }
    }

    public function storeIsSpecial(StoreProductSpecialRequest $request)
    {
        try {
            $product = Product::create($request->all());
            return sendResponse(new ProductResource($product));
        } catch (\Exception $e) {
            return sendResponse(null, $e->getMessage());
        }
    }

    public function show($id)
    {
        $product = Product::where('code', $id)->first();
        return sendResponse(new ProductResource($product));
    }

    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->fill($request->all())->save();

        return sendResponse($product);
    }

    public function out()
    {
    }
}
