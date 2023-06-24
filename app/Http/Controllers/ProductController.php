<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\StoreProductOutRequest;
use App\Models\Product;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;

class ProductController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $products = ProductResource::collection(Product::all());
        return sendResponse($products);
    }

    public function toBuy(Request $request)
    {
        $model = $request->model;
        $state_id = $request->state_id;

        $products = Product::withCount([
                "$model as count" => function ($query) use ($state_id) {
                    $query->where('state_id', $state_id);
                }
            ])
            ->withSum(["$model as sum_amount" => function ($query) use ($state_id) {
                $query->where('state_id', $state_id);
            }], 'amount')

            /* Opcional: para asegurarse de que haya al menos una relación con PriceQuoteProduct en el estado filtrado */
            ->having('count', '>', 0)
            ->orderBy('count', 'desc')
            ->get();

        return sendResponse(ProductResource::collection($products));
    }

    public function inPedidoOnline()
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

            /* Opcional: para asegurarse de que haya al menos una relación con PriceQuoteProduct en el estado filtrado */
            ->having('count_order', '>', 0)
            ->orderBy('count_order', 'desc')
            ->get();

        return sendResponse($products);
    }

    public function store(StoreProductRequest $request)
    {
        try {
            $product = Product::create($request->all());
            return sendResponse($product);
        } catch (\Exception $e) {
            return sendResponse(null, $e->getMessage());
        }
    }

    public function storeOutCatalogue(StoreProductOutRequest $request)
    {
        try {
            $product = Product::create($request->all());
            return sendResponse($product);
        } catch (\Exception $e) {
            return sendResponse(null, $e->getMessage());
        }
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return sendResponse($product);
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
