<?php

namespace App\Http\Controllers;

use App\Models\Jazz\ProductoJazzTemp;
use App\Models\ProductJazz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JazzController extends Controller
{
    public function getProducts()
    {
        return sendResponse([
            'no_requiere' => ProductoJazzTemp::where('state', 'no_requiere')->get(),
            'requiere' => ProductoJazzTemp::where('state', 'requiere')->get(),
            'nuevo' => ProductoJazzTemp::where('state', 'nuevo')->get(),
        ]);
    }

    public function syncProductTemp()
    {
        DB::statement("DELETE FROM product_jazz_temp");

        $comodines = DB::connection('jazz')->table('comodines')
            ->join('comodinesvalores', 'comodines.IdComodin', '=', 'comodinesvalores.IdComodin')
            ->select('comodines.Nombre')
            ->distinct()
            ->pluck('Nombre');

        $listas = DB::connection('jazz')->table('precios_venta')
            ->select('idLista')
            ->distinct()
            ->pluck('idLista');

        $columnasPrecios = $listas->map(function ($id) {
            return "MAX(CASE WHEN pv.idLista = $id THEN pv.Precio END) AS precio_lista_$id";
        });

        $columnasComodines = $comodines->map(function ($nombre) {
            $col = preg_replace('/[\s\-\.]+/', '_', $nombre);
            return "MAX(CASE WHEN c.Nombre = '$nombre' THEN cv.Valor END) AS `$col`";
        });

        $selects = collect(
            [
                'p.IdProducto',
                'p.numero',
                'p.Nombre',
                'p.FechaMOD AS fecha_mod',
                'p.FechaALTA AS fecha_alta',
                'm.Codigo as codigo_marca',
                DB::raw("(
                    SELECT SUM(fa.Cantidad *
                        CASE WHEN f.Tipo IN (3, 4) THEN 1 ELSE -1 END)
                    FROM facturas_articulos fa
                    JOIN facturas f ON f.NroInterno = fa.NroInterno
                    WHERE fa.IdProducto = p.IdProducto) AS stock"),
                DB::raw("(
                    SELECT CodigoProducto
                    FROM productosproveedores pp
                    WHERE pp.IdProducto = p.IdProducto
                    AND pp.CostoEstandar = (
                        SELECT preciocostoestandar
                        FROM productoscombinacionescabecera p2
                        WHERE p2.IdProducto = p.IdProducto
                    )
                    LIMIT 1
                    ) AS provider_code")
            ]
        )
            ->merge($columnasPrecios)
            ->merge($columnasComodines)
            ->implode(",\n    ");

        $sqlFinal = "
                SELECT
                    $selects
                FROM productos p
                LEFT JOIN precios_venta pv ON p.IdProducto = pv.IdProducto
                LEFT JOIN comodinesvalores cv ON p.IdProducto = cv.IdCampo
                LEFT JOIN comodines c ON cv.IdComodin = c.IdComodin
                LEFT JOIN productoscombinacionescabecera pcc on pcc.IdProducto  = p.IdProducto
                LEFT JOIN marcas m on m.IdMarca = pcc.Marca
                GROUP BY p.IdProducto, p.numero, p.Nombre, p.FechaMOD, p.FechaALTA, codigo_marca
                ";

        LOG::info($sqlFinal);
        $resultado = DB::connection('jazz')->select($sqlFinal);
        $count = $this->seedProductTemp($resultado);

        return sendResponse("Analizando $count productos...");
    }

    public function seedProductTemp($resultado)
    {
        $collection = collect($resultado);

        // Filtrar por CODIGO_ORIGINAL no vacío
        $filtrados = $collection->filter(function ($row) {
            return !empty($row->CODIGO_ORIGINAL);
        });

        // Mapear a estructura de la tabla
        $rows = $filtrados->map(function ($row) {
            $r = (array) $row;

            return [
                'id' => $r['IdProducto'] ?? null,
                'nombre' => $r['Nombre'] ?? null,
                'code' => $r['numero'] ?? null,
                'provider_code' => $r['provider_code'] ?? null,
                'equivalence' => $r['EQUIVALENCIA'] ?? null,
                'observation' => $r['OBSERVACION'] ?? null,
                'ubicacion' => $r['UBICACION'] ?? null,
                'stock' => (int) ($r['stock'] ?? 0),
                'codigo_marca' => $r['codigo_marca'] ?? null,
                'precio_lista_2' => (float) ($r['precio_lista_2'] ?? 0),
                'precio_lista_3' => (float) ($r['precio_lista_3'] ?? 0),
                'precio_lista_6' => (float) ($r['precio_lista_6'] ?? 0),
                'fecha_alta' => $r['fecha_alta'] ?? now(),
                'fecha_mod' => $r['fecha_mod'] ?? now(),
                'state' => 'en_proceso',
            ];
        })->values();

        $rowsArray = $rows->all();
        $chunks = array_chunk($rowsArray, 1000);

        foreach ($chunks as $chunk) {
            DB::table('product_jazz_temp')->insert($chunk);
        }

        return $rows->count();
    }

    public function procesarTemporal()
    {
        $resultProcess = ProductJazz::processTemp();

        return sendResponse([
            'result_process' => $resultProcess,
            'products' => [
                'no_requiere' => ProductoJazzTemp::where('state', 'no_requiere')->with('brand')->get(),
                'requiere' => ProductoJazzTemp::where('state', 'requiere')->with('brand')->get(),
                'nuevo' => ProductoJazzTemp::where('state', 'nuevo')->with('brand')->get(),
            ]
        ]);
    }

    public function sync(Request $request)
    {
        $array_ids = $request->ids;

        if (empty($array_ids) || !is_array($array_ids)) {
            return sendResponse(null, 'No se enviaron IDs válidos para sincronizar', 400);
        }

        $sinc_id = DB::table('product_jazz_history')->max('sinc_id') + 1;

        DB::table('product_jazz_temp')
            ->whereIn('id', $array_ids)
            ->orderBy('id')
            ->chunk(300, function ($chunk) use ($sinc_id) {
                $rows = $chunk->map(fn($r) => (array) $r)->all();
                $ids = array_column($rows, 'id');

                // Obtener productos existentes
                $existingProducts = DB::table('product_jazz')
                    ->whereIn('id', $ids)
                    ->get()
                    ->map(function ($product) use ($sinc_id) {
                        $array = (array) $product;
                        unset($array['created_at'], $array['updated_at']);
                        $array['sinc_id'] = $sinc_id;
                        return $array;
                    })
                    ->all();

                // Guardar los existentes en el historial
                if (!empty($existingProducts)) {
                    DB::table('product_jazz_history')->insert($existingProducts);
                }

                // Preparar datos para upsert
                $rowsToUpsert = array_map(function ($r) {
                    unset($r['state']);
                    return $r;
                }, $rows);

                DB::table('product_jazz')->upsert(
                    $rowsToUpsert,
                    'id',
                    [
                        'nombre',
                        'code',
                        'provider_code',
                        'equivalence',
                        'observation',
                        'ubicacion',
                        'stock',
                        'precio_lista_2',
                        'precio_lista_3',
                        'precio_lista_6',
                        'fecha_alta',
                        'fecha_mod',
                        'codigo_marca',
                    ]
                );
            });

        $this->relacionarProductosPorCode($array_ids);

        return sendResponse('ok');
    }

    public function relacionarProductosPorCode($array_ids)
    {
        DB::table('products')
            //->whereNull('idProducto')
            ->whereIn('idProducto', $array_ids)
            ->whereNotNull('code')
            ->orderBy('id')
            ->chunk(500, function ($products) {
                $codes = $products->pluck('code')->unique()->toArray();

                // Hacemos join directo: product_jazz → product_brands
                $productJazz = DB::table('product_jazz as pj')
                    ->leftJoin('product_brands as pb', 'pj.codigo_marca', '=', 'pb.code')
                    ->whereIn('pj.code', $codes)
                    ->select('pj.code', 'pj.id as idProducto', 'pb.id as product_brand_id', 'pj.provider_code as provider_code', 'pj.equivalence as equivalence')
                    ->get();

                // Mapeamos por code
                $jazzMap = $productJazz->keyBy('code');

                $updates = [];
                foreach ($products as $product) {
                    if (isset($jazzMap[$product->code])) {
                        $a = $jazzMap[$product->code];
                        $updates[] = [
                            'id' => $product->id,
                            'idProducto' => $jazzMap[$product->code]->idProducto,
                            'product_brand_id' => $jazzMap[$product->code]->product_brand_id,
                            'provider_code' => $jazzMap[$product->code]->provider_code,
                            'equivalence' => $jazzMap[$product->code]->equivalence
                        ];
                    }
                }

                // Ejecutamos updates
                foreach ($updates as $update) {
                    DB::table('products')
                        ->where('id', $update['id'])
                        ->update([
                            'idProducto' => $update['idProducto'],
                            'product_brand_id' => $update['product_brand_id'],
                            'provider_code' => $update['provider_code'],
                            'equivalence' => $update['equivalence'],
                        ]);
                }
            });

        return sendResponse('Relaciones actualizadas exitosamente.');
    }

    public function updateStockNadPrices()
    {
        DB::statement("DELETE FROM product_jazz_temp");

        $listas = DB::connection('jazz')->table('precios_venta')
            ->select('idLista')
            ->distinct()
            ->pluck('idLista');

        $columnasPrecios = $listas->map(function ($id) {
            return "MAX(CASE WHEN pv.idLista = $id THEN pv.Precio END) AS precio_lista_$id";
        });

        $selects = collect([
            'p.IdProducto',
            'p.numero',
            'p.Nombre',
            DB::raw("(
                    SELECT SUM(fa.Cantidad *
                        CASE WHEN f.Tipo IN (3, 4) THEN 1 ELSE -1 END)
                    FROM facturas_articulos fa
                    JOIN facturas f ON f.NroInterno = fa.NroInterno
                    WHERE fa.IdProducto = p.IdProducto
                ) AS stock")
        ])
            ->merge($columnasPrecios)
            ->implode(",\n    ");

        $sqlFinal = "
                SELECT
                    $selects
                FROM productos p
                LEFT JOIN precios_venta pv ON p.IdProducto = pv.IdProducto
                GROUP BY p.IdProducto, p.numero, p.Nombre";

        $resultado = DB::connection('jazz')->select($sqlFinal);

        $total = count($resultado);
        $current = 0;

        foreach ($resultado as $row) {
            $current++;
            // Debug: mostrar el producto antes de actualizar

            // Ejecutar el update y guardar cantidad de filas afectadas
            $updated = DB::table('product_jazz')
                ->where('id', $row->IdProducto)
                ->update([
                    'stock' => $row->stock ?? 0,
                    'precio_lista_2' => $row->precio_lista_2 ?? 0,
                    'precio_lista_3' => $row->precio_lista_3 ?? 0,
                    'precio_lista_6' => $row->precio_lista_6 ?? 0,
                ]);

            // Debug: mostrar si actualizó algo
            echo "Procesado $current / $total \n";
        }
    }
}
