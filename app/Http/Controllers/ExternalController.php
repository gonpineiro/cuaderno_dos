<?php

namespace App\Http\Controllers;

use App\Services\JazzServices\ApiService;
use App\Services\JazzServices\ProductService;
use Illuminate\Http\Request;

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
            $datos = $this->apiService->getStock(1);
            return response()->json($datos);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
