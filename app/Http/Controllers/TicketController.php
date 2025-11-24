<?php

namespace App\Http\Controllers;

use App\Http\Requests\Vehiculo\StoreVehiculoRequest;
use App\Http\Requests\Vehiculo\UpdateVehiculoRequest;
use App\Http\Resources\Ticket\TicketResource;
use App\Http\Resources\VehiculoResource;
use App\Models\PriceQuote;
use App\Models\Table;
use App\Models\Ticket;
use App\Models\Vehiculo;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::all();
        return sendResponse(TicketResource::collection($tickets));
    }

    public function store(StoreVehiculoRequest $request)
    {
        $vehiculo = Vehiculo::create($request->all());
        return sendResponse(new VehiculoResource($vehiculo));
    }

    public function generar_ticket(Request $request)
    {
        $modelInstance = Ticket::modelMap($request->model);
        $model = $modelInstance::find($request->model_id);

        if (!$model) {
            return sendResponse(null, "No se encontro el modelo: $request->model; id: $request->model_id");
        }

        $state = Table::where('name', 'ticket_estado')->where('value', 'abierto')->first();
        $ticket =  $model->tickets()->create([
            'user_id'     => auth()->id(),
            'prioridad_id' => $request->prioridad_id,
            'estado_id' => $state->id,
            'titulo'      => $request->titulo,
            'descripcion' => $request->descripcion,
        ]);

        return sendResponse($ticket);
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
