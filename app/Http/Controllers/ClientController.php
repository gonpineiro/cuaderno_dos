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
