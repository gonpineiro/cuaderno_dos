<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Http\Requests\City\StoreCityRequest;
use App\Http\Requests\City\UpdateCityRequest;
use App\Http\Resources\CityResource;

class CityController extends \App\Http\Controllers\Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return sendResponse(CityResource::collection(City::all()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\City\StoreCityRequest  $request
     * @return \App\Http\Resources\CityResource|\Illuminate\Http\JsonResponse
     */
    public function store(StoreCityRequest $request)
    {
        $city = City::create($request->all());
        return new CityResource($city);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Order\StoreOrderRequest  $request
     * @return \App\Http\Resources\OrderResource|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $city = City::findOrFail($id);
        return sendResponse(new CityResource($city));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Order\UpdateOrderRequest $request
     * @param  \App\Models\Api\Order $city
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCityRequest $request, $id)
    {
        $city = City::findOrFail($id);
        $city->fill($request->all())->save();
        return sendResponse(new CityResource($city));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $city = City::findOrFail($id);
        $city->delete();
        return sendResponse(new CityResource($city));
    }
}
