<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\ProductJazzResource;
use App\Models\Product;
use App\Models\ProductJazz;
use App\Services\JazzServices\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductJazzController extends Controller
{
    public function detalle(Request $request)
    {
        try {
            $id = (int) $request->query('id');
            $product = Product::find($id);

            if (!$product) {
                return sendResponse(null, 'Producto no encontrado', 404);
            }

            if (!$product->idProducto) {
                return sendResponse(null, 'El producto no tiene relación con Jazz', 404);
            }

            $jazzProduct = DB::connection('jazz')->selectOne(<<<'SQL'
                SELECT
                    p.IdProducto AS id,
                    MAX(CASE WHEN c.Nombre = 'CODIGO ORIGINAL' THEN cv.Valor END) AS code,
                    MAX(CASE WHEN c.Nombre = 'CODIGO PROVEEDOR' THEN cv.Valor END) AS provider_code,
                    MAX(CASE WHEN c.Nombre = 'EQUIVALENCIA' THEN cv.Valor END) AS equivalence,
                    MAX(CASE WHEN c.Nombre = 'OBSERVACIONES' THEN cv.Valor END) AS observation,
                    MAX(CASE WHEN c.Nombre = 'UBICACION' THEN cv.Valor END) AS ubicacion,
                    p.Nombre AS nombre,
                    p.FechaALTA AS fecha_alta,
                    p.FechaMOD AS fecha_mod,
                    COALESCE((
                        SELECT SUM(
                            fa.Cantidad * CASE
                                WHEN f.CodigoPerfil = 6 THEN 0
                                WHEN f.AfectaStock = 'N' THEN 0
                                WHEN f.AfectaStock = 'S' AND f.Tipo IN (3, 4) THEN 1
                                WHEN f.AfectaStock = 'S' AND f.Tipo NOT IN (3, 4) THEN -1
                                ELSE 0
                            END
                        )
                        FROM facturas_articulos fa
                        JOIN facturas f ON f.NroInterno = fa.NroInterno
                        WHERE fa.IdProducto = p.IdProducto
                    ), 0) AS stock,
                    CAST(ROUND(COALESCE(MAX(CASE WHEN pv.idLista = 2 THEN pv.Precio END), 0), 0) AS UNSIGNED) AS precio_lista_2,
                    CAST(ROUND(COALESCE(MAX(CASE WHEN pv.idLista = 3 THEN pv.Precio END), 0), 0) AS UNSIGNED) AS precio_lista_3,
                    CAST(ROUND(COALESCE(MAX(CASE WHEN pv.idLista = 6 THEN pv.Precio END), 0), 0) AS UNSIGNED) AS precio_lista_6
                FROM productos p
                LEFT JOIN precios_venta pv ON pv.IdProducto = p.IdProducto
                LEFT JOIN comodinesvalores cv ON cv.IdCampo = p.IdProducto
                LEFT JOIN comodines c ON c.IdComodin = cv.IdComodin
                WHERE p.IdProducto = ?
                GROUP BY p.IdProducto, p.Nombre, p.FechaALTA, p.FechaMOD
            SQL, [$product->idProducto]);

            if (!$jazzProduct) {
                return sendResponse(null, 'Producto Jazz no encontrado', 404);
            }

            return sendResponse($this->updateProductJazz((array) $jazzProduct));
        } catch (\Throwable $th) {
            return sendResponse(null, $th->getMessage(), 300);
        }
    }

    public function updateProductJazz($product): ProductJazz
    {
        if (!isset($product['idProducto'])) {
            $pj = ProductJazz::firstOrNew(['id' => $product['id']]);

            $pj->fill([
                'nombre' => $product['nombre'],
                'code' => $product['code'],
                'provider_code' => $product['provider_code'],
                'equivalence' => $product['equivalence'],
                'observation' => $product['observation'],
                'ubicacion' => $product['ubicacion'],
                'stock' => $product['stock'],
                'precio_lista_2' => (int) $product['precio_lista_2'],
                'precio_lista_3' => (int) $product['precio_lista_3'],
                'precio_lista_6' => (int) $product['precio_lista_6'],
                'fecha_alta' => $product['fecha_alta'],
                'fecha_mod' => $product['fecha_mod'],
            ]);

            $pj->save();

            return $pj;
        }

        $pj = ProductJazz::firstOrNew(['id' => $product['idProducto']]);

        $pj->nombre = $product['nombre'];
        $pj->stock = $product['totalStockDisponible'] ? $product['totalStockDisponible'] : 0;
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

    public function syncStock()
    {
        try {
            // Sin límite de tiempo de ejecución
            set_time_limit(0);

            // Sin límite de memoria
            ini_set('memory_limit', '-1');

            return sendResponse(ProductService::updateStockPrices());
        } catch (\Throwable $th) {
            return sendResponse(null, $th->getMessage());
        }
    }
}
