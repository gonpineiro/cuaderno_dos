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
        $clients = ClientResource::collection(Client::all());
        $count = count($clients);

        if ($request->query('compare_total') === $count) {
            return sendResponse('equals');
        }
        return sendResponse($clients);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Client\StoreClientRequest $request
     * @return \App\Http\Resources\ClientResource|\Illuminate\Http\JsonResponse
     */
    public function store(StoreClientRequest $request)
    {
        $body = $request->all();
        $client = Client::create($body);
        return new ClientResource($client);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Api\Client $client
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $client = Client::findOrFail($id);
        return sendResponse(new ClientResource($client));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Client\UpdateClientRequest $request
     * @param  \App\Models\Api\Client $client
     * @return \App\Http\Resources\ClientResource
     */
    public function update(UpdateClientRequest $request, $id)
    {
        $client = Client::findOrFail($id);
        $client->fill($request->all())->save();
        return new ClientResource($client);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Api\Client $client
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $client = Client::findOrFail($id);
            $client->delete();
            return sendResponse(new ClientResource($client));
        } catch (\Exception $th) {
            return sendResponse(null, $th->getMessage());
        }
    }
}
