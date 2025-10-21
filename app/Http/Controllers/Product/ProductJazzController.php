<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductJazzResource;
use App\Models\Product;
use App\Services\JazzServices\ProductService;

use App\Models\ProductJazz;
use Illuminate\Http\Request;

class ProductJazzController extends Controller
{
    public function detalle(Request $request)
    {
        try {
            $ps = new ProductService();
            //$stock = $ps->getStock($request->id);
            $product_ = $ps->getProduct($request->id);

            $pj = $this->updateProductJazz($product_);

            return sendResponse($pj);
        } catch (\Exception $th) {
            return sendResponse(null, $th->getMessage(), 300);
        }
    }

    public function updateProductJazz($product): ProductJazz
    {
        $pj = ProductJazz::firstOrNew(['id' => $product['idProducto']]);

        $pj->nombre = $product['nombre'];
        $pj->stock = $product['totalStockDisponible'];
        $pj->fecha_alta = $product['fechaAlta'];
        $pj->fecha_mod = $product['fechaMod'];

        // Extraer precios
        $pj->setPrices(collect($product['precios']));

        // Adicionales
        $pj->setAdicionales(collect($product['camposAdicionales']));

        $pj->save();

        return $pj;
    }

    public function index()
    {
        $sin_relacion = Product::whereNull('idProducto')->count();
        $con_relacion = Product::whereNotNull('idProducto')->count();
        $obtenidos = ProductJazz::count();

        $productos = ProductJazz::orderBy('stock', 'asc')->get();

        $data = [
            'sin_relacion' => $sin_relacion,
            'con_relacion' => $con_relacion,
            'obtenidos' => $obtenidos,
            'faltantes' => $con_relacion - $obtenidos,
            'productos' => ProductJazzResource::collection($productos),
        ];

        return sendResponse($data);
    }
}
