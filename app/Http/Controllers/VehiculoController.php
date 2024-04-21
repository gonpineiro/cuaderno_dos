<?php

namespace App\Http\Controllers;

use App\Http\Requests\Vehiculo\StoreVehiculoRequest;
use App\Http\Requests\Vehiculo\UpdateVehiculoRequest;
use App\Http\Resources\VehiculoResource;
use App\Models\Vehiculo;

class VehiculoController extends Controller
{
    public function index()
    {
        $vehiculos = Vehiculo::all();
        return sendResponse(VehiculoResource::collection($vehiculos));
    }

    public function store(StoreVehiculoRequest $request)
    {
        $vehiculo = Vehiculo::create($request->all());
        return sendResponse(new VehiculoResource($vehiculo));
    }

    public function show($id)
    {
        $vehiculo = Vehiculo::findOrFail($id);
        return sendResponse(new VehiculoResource($vehiculo));
    }

    public function update(UpdateVehiculoRequest $request, $id)
    {
        $vehiculo = Vehiculo::findOrFail($id);
        $vehiculo->fill($request->all())->save();
        return sendResponse(new VehiculoResource($vehiculo));
    }

    public function destroy($id)
    {
        $vehiculo = Vehiculo::findOrFail($id);
        $vehiculo->delete();
        return sendResponse(new VehiculoResource($vehiculo));
    }
}
