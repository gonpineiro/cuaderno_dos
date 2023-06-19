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
}
