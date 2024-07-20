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
        try {
            $coeficientes = $request->coeficientes;

            DB::beginTransaction();

            foreach ($coeficientes as $coef) {
                if (isset($coef['new']) && $coef['new']) {
                    Coeficiente::create($coef);
                } else {
                    if (!$_coeficiente = Coeficiente::find($coef['id'])) {
                        throw new \Exception("Coeficiente " . $coef['id'] . " no encontrado");
                    }

                    if (isset($coef['deleted']) && $coef['deleted']) {
                        $_coeficiente->delete();
                    } else {
                        $_coeficiente->update($coef);
                    }
                }
            }

            DB::commit();

            $coeficientes  = Coeficiente::orderBy('position', 'asc')->get();

            return sendResponse(CoeficienteResource::collection($coeficientes));
        } catch (\Exception $e) {
            return sendResponse(null, $e->getMessage(), 300);
        }
    }
}
