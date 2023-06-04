<?php

namespace App\Http\Controllers\Api;

use App\Models\Api\Provider;
use App\Http\Requests\Provider\StoreProviderRequest;
use App\Http\Requests\Provider\UpdateProviderRequest;
use App\Http\Resources\ProviderResource;

class ProviderController extends \App\Http\Controllers\Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return sendResponse(ProviderResource::collection(Provider::all()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Provider\StoreProviderRequest  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\JsonResponse
     */
    public function store(StoreProviderRequest $request)
    {
        $provider = Provider::create($request->all());
        return sendResponse(new ProviderResource($provider));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Api\Provider  $provider
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $provider = Provider::findOrFail($id);
        return sendResponse(new ProviderResource($provider));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Provider\UpdateProviderRequest  $request
     * @param  \App\Models\Api\Provider  $provider
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProviderRequest $request, $id)
    {
        $provider = Provider::findOrFail($id);
        $provider->fill($request->all())->save();
        return sendResponse(new ProviderResource($provider));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Api\Provider  $provider
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $provider = Provider::findOrFail($id);
        $provider->delete();
        return sendResponse(new ProviderResource($provider));
    }
}
