<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateStockPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:stock-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualizar stock y listas de precios de productos desde jazz';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Iniciando actualización de stock y precios...");

        $this->updateStockPrices();

        $this->updateProductsBrands();

        $this->info("Actualización completada.");
    }

    private function updateStockPrices()
    {
        try {
            activity('success.updateStockPrices')->log('Inicio');
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

                $percent = number_format(($current / $total) * 100, 2);
                $this->info("Procesado $current / $total ($percent%)");
            }
            activity('success.updateStockPrices')
                ->withProperties(['total' => $total])
                ->log('Proceso finalizado');
        } catch (\Exception $e) {
            activity('error.updateStockPrices')
                ->withProperties([
                    'command' => $this->signature,
                    'error' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                ])
                ->log('Error en limpieza de idProducto');
        }
    }

    private function updateProductsBrands()
    {
        $marcas = DB::connection('jazz')->table('marcas')
            ->select('idMarca', 'Empresa', 'Codigo', 'Nombre')
            ->get();

        // Transformo la colección a array para el upsert
        $data = $marcas->map(function ($item) {
            return [
                'idMarca' => $item->idMarca,
                'Empresa' => $item->Empresa,
                'Codigo'  => $item->Codigo,
                'Nombre'  => $item->Nombre,
            ];
        })->toArray();

        // Inserto o actualizo según corresponda (basado en idMarca)
        DB::connection('mysql')->table('product_brands_jazz')->upsert(
            $data,
            ['idMarca'], // clave única
            ['Empresa', 'Codigo', 'Nombre'] // columnas que se actualizan si existe
        );


        // Traigo de la tabla intermedia
        $brandsJazz = DB::table('product_brands_jazz')
            ->select('Codigo', 'Nombre')
            ->get();

        // Transformo para el upsert
        $data = $brandsJazz->map(function ($item) {
            return [
                'code' => $item->Codigo,
                'name' => $item->Nombre,
            ];
        })->toArray();

        // Inserto o actualizo en product_brands
        DB::table('product_brands')->upsert(
            $data,
            ['code'],      // clave única
            ['name']       // columnas que se actualizan si existe
        );

        activity('success.updateProductsBrands')->log('Proceso finalizado');
    }
}
