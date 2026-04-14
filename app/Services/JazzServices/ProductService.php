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
                ]);
        }

        return $total;
    }
}
