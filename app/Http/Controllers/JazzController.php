<?php

namespace App\Http\Controllers;

use App\Services\JazzService;
use Illuminate\Support\Facades\DB;

class JazzController extends Controller
{
    protected $jazzService;

    /* public function __construct(JazzService $jazzService)
    {
        $this->jazzService = $jazzService;
    } */

    public function index()
    {
        // Ejecutar una consulta SELECT
        $results = $this->jazzService->query('SELECT * FROM users WHERE active = ?', [1]);

        return response()->json($results);
    }

    public function store()
    {
        // Ejecutar una consulta INSERT
        $inserted = $this->jazzService->query(
            'INSERT INTO users (name, email) VALUES (?, ?)',
            ['John Doe', 'john@example.com']
        );

        return response()->json(['inserted' => $inserted]);
    }

    public function get_stock_product()
    {
        // Ejecutar una consulta SELECT
        $query =
            'SELECT SUM(cantidad * signo) as stock
            FROM (
                SELECT fa.Cantidad, f.Tipo,
                    CASE WHEN tipo IN (3, 4) THEN 1 ELSE -1 END AS signo
                FROM facturas_articulos fa
                JOIN facturas f ON f.NroInterno = fa.NroInterno
                WHERE fa.IdProducto = ?
            ) AS subquery';

        $results = $this->jazzService->query($query, [50039187]);

        return response()->json($results);;
    }

    public function test()
    {
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
            'p.FechaALTA AS fecha_alta'
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

        return $resultado;
    }
}
