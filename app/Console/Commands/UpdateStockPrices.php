<?php

namespace App\Console\Commands;

use App\Services\JazzServices\ProductService;
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
            $total = ProductService::updateStockPrices();
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
