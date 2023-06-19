<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\StoreProductOutRequest;
use App\Models\Product;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;

class ProductController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $products = ProductResource::collection(Product::all());
        return sendResponse($products);
    }

    public function toBuy()
    {
        $products = Product::where('empty_stock', true)
            ->withCount([
                'priceQuoteProduct' => function ($query) {
                    $query->where('state_id', 13);
                }
            ])

            /* Opcional: para asegurarse de que haya al menos una relación con PriceQuoteProduct en el estado filtrado */
            ->having('price_quote_product_count', '>', 0)
            ->orderBy('price_quote_product_count', 'desc')
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
