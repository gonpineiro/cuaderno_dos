<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientChasisResource;
use App\Models\ClientChasis;
use Illuminate\Support\Facades\DB;

class ClientChasisController extends Controller
{
    public function index(Request $request)
    {
        $chasis  = ClientChasis::with(['client', 'vehiculo'])->where('vehiculo_id', $request->vehiculo_id)->get();
        return sendResponse(ClientChasisResource::collection($chasis));
    }

    public function cliente_chasis_update(Request $request)
    {
        $data = $request->input('data');
        $clientId = $request->input('client_id');

        DB::beginTransaction();

        try {
            // Eliminar los elementos que no están en el arreglo recibido
            $existingIds = collect($data)->pluck('id')->filter()->toArray();
            ClientChasis::where('client_id', $clientId)
                ->whereNotIn('id', $existingIds)
                ->delete();

            foreach ($data as $item) {
                if (isset($item['id']) && $item['id'] !== null) {
                    // Actualizar los elementos existentes
                    ClientChasis::where('id', $item['id'])
                        ->where('client_id', $clientId)
                        ->update([
                            'chasis' => $item['chasis'],
                            'vehiculo_id' => $item['vehiculo_id'],
                            'year' => $item['year'],
                        ]);
                } else {
                    $exists = ClientChasis::where('chasis', $item['chasis'])->exists();
                    if ($exists && $item['chasis']) {
                        throw new \Exception("El chasis '{$item['chasis']}' ya existe.");
                    }
                    // Crear nuevos elementos
                    ClientChasis::create([
                        'client_id' => $clientId,
                        'chasis' => $item['chasis'],
                        'vehiculo_id' => $item['vehiculo_id'],
                        'year' => $item['year'],
                    ]);
                }
            }


            DB::commit();

            $client_chasis = ClientChasis::where('client_id', $clientId)->with('vehiculo.brand')->get();

            return sendResponse($client_chasis);
        } catch (\Exception $e) {
            return sendResponse(null, $e->getMessage(), 301);
        }
    }
}
