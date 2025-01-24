<?php

namespace App\Http\Controllers;

use App\Services\JazzService;

class JazzController extends Controller
{
    protected $jazzService;

    public function __construct(JazzService $jazzService)
    {
        $this->jazzService = $jazzService;
    }

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
}
