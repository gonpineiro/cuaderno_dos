<?php

namespace App\Http\Controllers;

use App\Models\ProductBrand;
use Illuminate\Http\Request;

class ProductBrandController extends Controller
{
    public function index()
    {
        $brands = ProductBrand::all();
        return sendResponse($brands);
    }

    public function store(Request $request)
    {
        $brand = ProductBrand::create($request->all());
        return sendResponse($brand);
    }

    public function show($id)
    {
        $brand = ProductBrand::find($id);
        return sendResponse($brand);
    }

    public function update(Request $request, $id)
    {
        $brand = ProductBrand::find($id);
        $brand->update($request->all());
        $brand->save();

        return sendResponse($brand);
    }
}
