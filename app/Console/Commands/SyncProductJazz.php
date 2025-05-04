<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

use App\Services\JazzServices\ProductService;
use App\Models\Product;
use App\Http\Controllers\ProductController;

class SyncProductJazz extends Command
{
    protected $signature = 'sync:product-jazz';
    protected $description = 'Sincroniza todos los productos con ProductJazz usando la API';

    public function handle()
    {
        $this->info('⏳ Iniciando sincronización de productos...');
        $startGlobal = microtime(true);

        $log = Log::channel('sync_product_jazz');

        $productos = Product::whereNotNull('idProducto')
            ->whereNotIn('idProducto', function ($query) {
                $query->select('id')->from('product_jazz');
            })
            ->select('idProducto')
            ->get();

        $total = $productos->count();

        $ps = new ProductService();
        $controller = new ProductController();

        $errores = [];
        $exitosos = 0;
        $tiempos = [];

        foreach ($productos as $producto) {
            $start = microtime(true);

            try {
                $data = $ps->getProduct($producto->idProducto);
                $controller->updateProductJazz($data);
                $this->line("✔ Producto {$producto->idProducto} sincronizado.");
                $exitosos++;
            } catch (\Exception $e) {
                $this->error("✖ Error al sincronizar producto {$producto->idProducto}: " . $e->getMessage());
                $log->error("❌ Sync error for product {$producto->idProducto}", [
                    'error' => $e->getMessage()
                ]);
                $errores[] = $producto->idProducto;
            }

            $tiempos[] = microtime(true) - $start;
        }

        $tiempoTotal = microtime(true) - $startGlobal;
        $tiempoPromedio = count($tiempos) ? array_sum($tiempos) / count($tiempos) : 0;

        $this->info("🎯 Sincronización finalizada.");
        $this->info("⏱ Tiempo total: " . round($tiempoTotal, 2) . " segundos");
        $this->info("⏱ Tiempo promedio por producto: " . round($tiempoPromedio, 2) . " segundos");
        $this->info("✅ Productos exitosos: $exitosos");
        $this->info("❌ Productos con error: " . count($errores));

        if (count($errores)) {
            $this->warn("🧾 IDs con error: " . implode(', ', $errores));
        }

        $log->info('📋 Resultado de sync:');
        $log->info("🕒 Tiempo total: {$tiempoTotal}s");
        $log->info("🕒 Tiempo promedio: {$tiempoPromedio}s");
        $log->info("✔️ Exitosos: {$exitosos}");
        $log->info("❌ Errores (" . count($errores) . "): " . implode(', ', $errores));
    }
}
