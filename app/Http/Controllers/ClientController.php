<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\{StoreClientRequest, UpdateClientRequest};
use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\Request;

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
        return new ClientResource($client, 'complete');
    }

    public function show($id)
    {
        $client = Client::findOrFail($id);
        return sendResponse(new ClientResource($client, 'complete'));
    }

    public function search(Request $request)
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
    }

    public function getByReference(Request $request)
    {
        $client = Client::where('reference_id', $request->id)->first();
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
