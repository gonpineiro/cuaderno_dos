<?php

namespace App\Http\Controllers;

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
            $configuracion = Coeficiente::find($coeficiente['id']);
            if ($configuracion) {
                $configuracion->update($coeficiente);
            }
        }

        DB::commit();

        return sendResponse($coeficientes);
    }
}
