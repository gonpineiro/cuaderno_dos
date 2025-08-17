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
            GROUP BY p.IdProducto, p.numero, p.Nombre
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
                ]);

            $percent = number_format(($current / $total) * 100, 2);
            $this->info("Procesado $current / $total ($percent%)");
        }

        $this->info("Actualización completada.");
    }
}
