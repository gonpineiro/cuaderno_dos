<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Http\Requests\City\StoreCityRequest;
use App\Http\Requests\City\UpdateCityRequest;
use App\Http\Resources\CityResource;

class CityController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $ciudades = City::all();
        return sendResponse(CityResource::collection($ciudades));
    }

    public function store(StoreCityRequest $request)
    {
        $city = City::create($request->all());
        return sendResponse(new CityResource($city));
    }

    public function show($id)
    {
        $city = City::findOrFail($id);
        return sendResponse(new CityResource($city));
    }

    public function update(UpdateCityRequest $request, $id)
    {
        $city = City::findOrFail($id);
        $city->fill($request->all())->save();
        return sendResponse(new CityResource($city));
    }

    public function destroy($id)
    {
        $city = City::findOrFail($id);
        $city->delete();
        return sendResponse(new CityResource($city));
    }
}
