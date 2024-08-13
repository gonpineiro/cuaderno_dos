<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\{StoreClientRequest, UpdateClientRequest};
use App\Http\Resources\ClientResource;
use App\Models\Client;

class ClientController extends Controller
{
    public function index()
    {
        $clients = ClientResource::collection(Client::all());
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
                } else if($value) {
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
    /* public function search(Request $request)
    {
        $model = new Client();

        $attributes = $model->getFillable();

        $clients = Client::query();

        foreach ($attributes as $attribute) {
            $clients->orWhere($attribute, 'LIKE', '%' . $request->string . '%');
        }

        $results = $clients->get();

        if (!$results) {
            return sendResponse(null, 'No se encontro un resultado de busqueda');
        }
        return sendResponse(ClientResource::collection($results));
    } */

    public function getByReference(Request $request)
    {
        $client = Client::where('reference_id', "ID" . $request->id)->first();
        if ($client) {
            return sendResponse(new ClientResource($client, 'complete'));
        }
        return sendResponse(null, 'Ciente no encontrado', 404);
    }

    public function update(UpdateClientRequest $request, $id)
    {
        $client = Client::findOrFail($id);
        $client->fill($request->all())->save();
        return new ClientResource($client, 'complete');
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
