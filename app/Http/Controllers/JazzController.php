<?php

namespace App\Http\Controllers;

use App\Models\Jazz\ProductoJazzTemp;
use App\Models\ProductJazz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $selects = collect([
            'p.IdProducto',
            'p.numero',
            'p.Nombre',
            'p.FechaMOD AS fecha_mod',
            'p.FechaALTA AS fecha_alta',
            DB::raw("(
            SELECT SUM(fa.Cantidad *
                CASE WHEN f.Tipo IN (3, 4) THEN 1 ELSE -1 END)
            FROM facturas_articulos fa
            JOIN facturas f ON f.NroInterno = fa.NroInterno
                WHERE fa.IdProducto = p.IdProducto) AS stock")
        ])
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
                GROUP BY p.IdProducto, p.numero, p.Nombre, p.FechaMOD, p.FechaALTA
                ";

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
                'provider_code' => $r['CODIGO_PROVEEDOR'] ?? null,
                'equivalence' => $r['EQUIVALENCIA'] ?? null,
                'observation' => $r['OBSERVACION'] ?? null,
                'ubicacion' => $r['UBICACION'] ?? null,
                'stock' => (int) ($r['stock'] ?? 0),
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
                'no_requiere' => ProductoJazzTemp::where('state', 'no_requiere')->get(),
                'requiere' => ProductoJazzTemp::where('state', 'requiere')->get(),
                'nuevo' => ProductoJazzTemp::where('state', 'nuevo')->get(),
            ]
        ]);
    }

    public function sync(Request $request)
    {

        $array_ids = $request->ids;

        if (empty($array_ids) || !is_array($array_ids)) {
            return sendResponse(null, 'No se enviaron IDs válidos para sincronizar', 400);
        }
        DB::table('product_jazz_temp')
            ->whereIn('id', $array_ids)
            ->orderBy('id')
            ->chunk(300, function ($chunk) {
                $rows = $chunk->map(function ($r) {
                    $array = (array) $r;
                    unset($array['state']);
                    return $array;
                })->all();


                DB::table('product_jazz')->upsert(
                    $rows,
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
                    ]
                );
            });

        return sendResponse('ok');
    }
}
