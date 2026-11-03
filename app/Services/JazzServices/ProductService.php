<?php

namespace App\Services\JazzServices;

use Illuminate\Support\Facades\DB;

class ProductService extends ApiService
{
    public function sayHello()
    {
        return $this->get('Producto/SayHello');
    }

    public function listProducts(string $empresa)
    {
        return $this->get("Producto/ListadoProductos/{$empresa}");
    }

    public function getProduct(int $id)
    {
        return $this->get("Producto/ConsultarProducto/{$id}");
    }

    public function getStock(int $id)
    {
        return $this->get("Producto/ConsultarStockDeProducto/{$id}");
    }

    public function updatePrice(int $id, int $lista, float $precio)
    {
        return $this->post("Producto/ActualizarPrecioDeUnProducto/{$id},{$lista},{$precio}", []);
    }

    public function listDiscounts()
    {
        return $this->post("Producto/ListarDescuentosPorCantidad", []);
    }

    public function listSuppliers()
    {
        return $this->get("Producto/ListarProveedoresDeProductos");
    }

    public static function updateStockPrices()
    {
        DB::table('product_jazz')->update(['is_updated' => 0]);

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
            'pcc.StockMin as stock_min',
            'pcc.StockMax as stock_max',
            'pcc.PuntoPedido as punto_pedido',
            DB::raw("(
                    SELECT SUM(
                        fa.Cantidad *
                        CASE
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
                ) AS stock")
        ])
            ->merge($columnasPrecios)
            ->implode(",\n    ");

        $sqlFinal = "
            SELECT
                $selects
            FROM productos p
            LEFT JOIN precios_venta pv ON p.IdProducto = pv.IdProducto
            LEFT JOIN productoscombinacionescabecera pcc on pcc.IdProducto = p.IdProducto
            GROUP BY p.IdProducto, p.numero, p.Nombre, stock_min, stock_max, punto_pedido
        ";

        $resultado = DB::connection('jazz')->select($sqlFinal);

        $total = count($resultado);
        $current = 0;

        foreach ($resultado as $row) {
            $current++;

            DB::table('product_jazz')
                ->where('id', $row->IdProducto)
                ->update([
                    'stock' => $row->stock ?? 0,
                    'precio_lista_2' => $row->precio_lista_2 ?? 0,
                    'precio_lista_3' => $row->precio_lista_3 ?? 0,
                    'precio_lista_6' => $row->precio_lista_6 ?? 0,
                    'stock_min' => $row->stock_min ?? 0,
                    'stock_max' => $row->stock_max ?? 0,
                    'punto_pedido' => $row->punto_pedido ?? 0,
                    'is_updated' => 1,
                ]);
        }

        self::normalizeProductLocations();

        return $total;
    }

    public static function normalizeProductLocations()
    {
        return DB::affectingStatement(<<<'SQL'
            UPDATE products p
            JOIN product_jazz pj ON pj.id = p.idProducto AND pj.code = p.code
            SET
                p.ship = TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(pj.ubicacion, 'NAVE:', -1), '-', 1)),
                p.module = TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(pj.ubicacion, 'MOD:', -1), '-', 1)),
                p.`column` = TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(pj.ubicacion, 'COL:', -1), '-', 1)),
                p.`row` = TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(pj.ubicacion, 'FILA:', -1), '-', 1)),
                p.side = TRIM(SUBSTRING_INDEX(pj.ubicacion, 'LADO:', -1))
            WHERE p.idProducto IS NOT NULL
                AND p.ship IS NULL
                AND TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(pj.ubicacion, 'NAVE:', -1), '-', 1)) IS NOT NULL
        SQL
        );
    }
}
