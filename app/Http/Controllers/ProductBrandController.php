<?php

namespace App\Http\Controllers;

use App\Models\ProductBrand;
use Illuminate\Http\Request;

class ProductBrandController extends Controller
{
    public function index()
    {
        $product_brands = ProductBrand::all();
        return sendResponse($product_brands);
    }

    public function store(Request $request)
    {
        $product_brand = ProductBrand::create($request->all());
        return sendResponse($product_brand);
    }

    public function show($id)
    {
        $product_brand = ProductBrand::find($id);
        return sendResponse($product_brand);
    }

    public function update(Request $request)
    {
        $product_brand = ProductBrand::find($request->id);
        $product_brand->update($request->all());
        $product_brand->save();

        return sendResponse($product_brand);
    }

    public function delete(Request $request)
    {
        $product_brand = ProductBrand::find($request->id);
        $product_brand->delete();
        return sendResponse($product_brand);
    }
}
