<?php

namespace App\Http\Controllers;

use App\Http\Resources\CoeficienteResource;
use App\Models\Coeficiente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CoeficienteController extends Controller
{
    public function store(Request $request)
    {
        $coeficientes = $request->coeficientes;

        DB::beginTransaction();

        foreach ($coeficientes as $coeficiente) {
            $_coeficiente = Coeficiente::find($coeficiente['id']);

            if ($_coeficiente && $coeficiente['deleted']) {
                $_coeficiente->delete();
            } else if ($_coeficiente) {
                $_coeficiente->update($coeficiente);
            }
        }

        DB::commit();

        $coeficientes  = Coeficiente::orderBy('position', 'asc')->get();

        return sendResponse(CoeficienteResource::collection($coeficientes));
    }
}
