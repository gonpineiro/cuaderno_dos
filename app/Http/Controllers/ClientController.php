<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\{StoreClientRequest, UpdateClientRequest};
use App\Http\Resources\Client\ClientJazzResource;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Models\Jazz\ClientJazz;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::orderBy('id', 'desc')->take(20)->get();
        $clients = ClientResource::collection($clients);
        return sendResponse($clients);
    }

    public function store(StoreClientRequest $request)
    {
        $body = $request->all();

        $client = Client::create($body);
        /*  $client->update(['reference_id', "ID" . $client->id]); */
        $client->reference_id = "ID" . $client->id;
        $client->save();
        return new ClientResource($client, 'complete');
    }

    public function show($id)
    {
        $client = Client::findOrFail($id);
        return sendResponse(new ClientResource($client, 'complete'));
    }

    public function search(Request $request/* , Model $model */)
    {
        // Obtén los datos de la solicitud
        $data = $request->all();

        // Obtén los atributos del modelo
        $model = new Client();
        $table = $model->getTable();
        $columns = Schema::getColumnListing($table);

        // Comienza la consulta base
        $query = $model->newQuery();

        // Agrega condiciones según los parámetros presentes y las columnas del modelo
        foreach ($columns as $column) {
            if (isset($data[$column])) {
                $value = $data[$column];

                // Ajustar las condiciones para diferentes tipos de datos
                if (is_string($value)) {
                    // Para cadenas de texto, usar 'like' para búsqueda parcial
                    $query->orWhere($column, 'like', "%$value%");
                } else if ($value) {
                    // Para otros tipos de datos, buscar coincidencia exacta
                    $query->where($column, $value);
                }
            }
        }
        // $query->where($column, 'like', "%$value%");

        // Obtén los resultados
        $results = $query->get();

        // Devuelve los resultados, podrías usar Resource para formatear la respuesta
        return sendResponse(ClientResource::collection($results));
    }

    public function searchJazz(Request $request)
    {
        $model = new ClientJazz();
        $columns = $model->getSearchColumns();

        $query = $model->newQuery();

        $search = $request->search;

        $query->where(function ($q) use ($columns, $search) {
            foreach ($columns as $column) {
                $q->orWhere($column, 'like', "%{$search}%");
            }
        });

        $results = $query->get();

        return sendResponse(ClientJazzResource::collection($results));
    }

    public function relacionarClienteJazz(Request $request)
    {
        $cliente = Client::find($request->cliente_id);

        if ($cliente->jazz_id) {
            return sendResponse(null, 'El cliente ya se encuentra relacioado');
        }

        $cliente->jazz_id = $request->jazz_id;
        $cliente->save();

        return sendResponse('Cliente asociado');
    }

    public function getByReference(Request $request)
    {
        $client = Client::where('reference_id', "ID" . $request->id)->first();
        if ($client) {
            return sendResponse(new ClientResource($client, 'complete'));
        }
        return sendResponse(null, 'Ciente no encontrado', 404);
    }

    public function update(UpdateClientRequest $request)
    {
        try {
            $client = Client::findOrFail($request->id);
            $client->fill($request->all())->save();

            return sendResponse(new ClientResource($client, 'complete'));
        } catch (\Exception $e) {
            return sendResponse(null, $e->getMessage(), 301);
        }
    }

    public function destroy($id)
    {
        try {
            $client = Client::findOrFail($id);
            $client->delete();
            return sendResponse(new ClientResource($client, 'complete'));
        } catch (\Exception $th) {
            return sendResponse(null, $th->getMessage());
        }
    }
}
