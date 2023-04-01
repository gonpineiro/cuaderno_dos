<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\{StoreClientRequest, UpdateClientRequest};
use App\Http\Resources\ClientResource;
use App\Models\Api\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $products = ClientResource::collection(Client::all());
        $count = count($products);

        if ($request->query('compare_total') == $count) {
            return sendResponse('equals');
        }
        return $products;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Client\StoreClientRequest  $request
     * @return \App\Http\Resources\ClientResource|\Illuminate\Http\JsonResponse
     */
    public function store(StoreClientRequest $request)
    {
        $product = Client::create($request->all());
        return new ClientResource($product);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Api\Client  $product
     * @return \App\Http\Resources\ClientResource
     */
    public function show($id)
    {
        $product = Client::findOrFail($id);
        return new ClientResource($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Client\UpdateClientRequest  $request
     * @param  \App\Models\Api\Client  $product
     * @return \App\Http\Resources\ClientResource
     */
    public function update(UpdateClientRequest $request, $id)
    {
        $product = Client::findOrFail($id);
        $product->fill($request->all())->save();
        return new ClientResource($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Api\Client  $product
     * @return \App\Http\Resources\ClientResource
     */
    public function destroy($id)
    {
        $product = Client::findOrFail($id);
        $product->delete();
        return new ClientResource($product);
    }
}
