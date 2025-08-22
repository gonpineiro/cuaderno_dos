<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientConfig;
use Illuminate\Http\Request;

class ClientConfigController extends Controller
{
    public function saveConfig(Request $request)
    {
        $data = $request->all();

        $clientConfig = ClientConfig::where('type', $request->type)
            ->where('client_id', $request->client_id)
            ->first();

        if ($clientConfig) {
            $clientConfig->delete();
        }

        $client = Client::find($request->client_id);
        $client->is_company = $request->is_company;
        $client->is_insurance = $request->is_insurance;
        $client->is_sin_vehiculo = $request->is_sin_vehiculo;
        $client->condicion_iva_id = $request->client_condicion_iva_id;
        $client->save();

        $clientConfig = ClientConfig::create($data);

        return sendResponse($data);
    }
}
