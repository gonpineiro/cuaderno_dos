<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\StoreProductSimpleRequest;
use App\Http\Resources\Product\ProductCotizacionesResource;
use App\Models\Product;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\StoreProductSpecialRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\PriceQuote\PriceQuoteResource;
use App\Http\Resources\Product\PedirResource;
use App\Http\Resources\Product\ProductResource;
use App\Models\Order;
use App\Models\PriceQuoteProduct;
use App\Models\OrderProduct;
use App\Models\Table;
use App\Models\ToAsk;
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
                if ($state_id == 'pedido') {
                    $query->where('state_id', 4)->orWhere('state_id', 8);
                } else {
                    $query->where('state_id', $state_id);
                }
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

    public function getInCotizaciones(Request $request)
    {
        $model = new Product();

        $attributes = $model->getFillable();
        $products = Product::query();

        $type = $request->type;
        if (!$type) {
            $products = $products->whereHas('price_quotes')
                ->distinct()
                ->where(function ($query) use ($attributes, $request) {
                    foreach ($attributes as $attribute) {
                        $query->orWhere($attribute, 'LIKE', '%' . $request->string . '%');
                    }
                });
        }

        if ($type === 'vehiculos') {
            $products = $products->whereHas('price_quotes', function ($query) use ($request) {
                $query->whereHas('vehiculo', function ($innerQuery) use ($request) {
                    $innerQuery->where('name', 'LIKE', '%' . $request->string . '%');
                });
            })
                ->distinct();
        }

        $products = $products->get();

        $col = ProductCotizacionesResource::collection($products);
        $col = $col->sortByDesc(function ($producto) {
            return $producto->cantidad_cotizaciones;
        });

        return sendResponse($col);
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

            $state = Table::where('name', 'price_quote_state')->where('value', 'cotizar')->first();
            $pq = PriceQuoteProduct::where('product_id', $product->id)
                ->where('state_id', $state->id)->with('price_quote')->get();

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

            /*  if ($model == 'pedidos_online') {
                $state = Table::where('name', 'order_online_state')->where('value', 'pendiente')->first();
            } else if ($model == 'pedidos_cliente') {
                $state = Table::where('name', 'order_cliente_state')->where('value', 'incompleto')->first();
            } else if ($model == 'pedidos_siniestro') {
                $state = Table::where('name', 'order_siniestro_state')->where('value', 'incompleto')->first();
            } else if ($model == 'cotizaciones') {
                return $this->cotizaciones($request);
            } */

            $product = Product::where('code', $code)->first();

            $pq = OrderProduct::where('product_id', $product->id)->with('order')->get();

            $orders = $pq->map(function ($item) {
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

    public function store(Request $request)
    {
        try {
            $body = $request->all();
            $product = Product::create($body);
            return sendResponse(new ProductResource($product));
        } catch (\Exception $e) {
            return sendResponse(null, $e->getMessage());
        }
    }

    /* public function storeIsSimple(StoreProductSimpleRequest $request)
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
            $body = $request->all();

            $state = Table::where('name', 'product_state')->where('value', 'sin_control_stock')->first();

            $body['state_id'] = $state->id;
            $body['is_special'] = true;

            $product = Product::create($body);
            return sendResponse(new ProductResource($product));
        } catch (\Exception $e) {
            return sendResponse(null, $e->getMessage());
        }
    } */

    public function show($id)
    {
        $products = Product::where(function ($query) use ($id) {
            $query->where('code', $id);
        })->first();

        if (!$products) {
            return sendResponse(null, 'No se encontro un resultado de busqueda');
        }
        return sendResponse(new ProductResource($products));
    }

    public function search(Request $request)
    {
        $model = new Product();

        $attributes = $model->getFillable();

        $products = Product::query();

        foreach ($attributes as $attribute) {
            $products->orWhere($attribute, 'LIKE', '%' . $request->string . '%');
        }

        $results = $products->get();

        if (!$results) {
            return sendResponse(null, 'No se encontro un resultado de busqueda');
        }
        return sendResponse(ProductResource::collection($results));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->fill($request->all())->save();

        return sendResponse(new ProductResource($product));
    }

    public function delete(Request $request)
    {
        try {
            $product = Product::findOrFail($request->id)->delete();
            return sendResponse('Producto eliminado');
        } catch (\Exception $e) {
            return sendResponse(null, $e->getMessage());
        }
    }
}
