<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::all();
        return sendResponse($brands);
    }

    public function store(Request $request)
    {
        $brand = Brand::create($request->all());
        return sendResponse($brand);
    }

    public function show($id)
    {
        $brand = Brand::find($id);
        return sendResponse($brand);
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::find($id);
        $brand->update($request->all());
        $brand->save();

        return sendResponse($brand);
    }
}
