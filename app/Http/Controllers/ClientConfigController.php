<?php

namespace App\Http\Controllers;

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

        $clientConfig = ClientConfig::create($data);

        return sendResponse($data);
    }
}
