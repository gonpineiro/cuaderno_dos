<?php

namespace App\Http\Controllers;

use App\Services\JazzServices\ProductService;

class ExternalController extends Controller
{
    protected $apiService;

    public function __construct()
    {
        $this->apiService = new ProductService();
    }

    public function obtenerDatos()
    {
        try {
            $datos = $this->apiService->listSuppliers();
            return response()->json($datos);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
