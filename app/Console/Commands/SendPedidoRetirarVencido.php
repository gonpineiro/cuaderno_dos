<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Table;
use App\Models\EmailLog;
use App\Http\TraitsControllers\TraitPedidosEmail;

class SendPedidoRetirarVencido extends Command
{
    protected $signature = 'orders:send-retirar-vencido {--dry-run}';
    protected $description = 'Envía email de pedido vencido a retirar a pedidos con +72h en estado retirar';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        
        $stateRetirarIds = Table::whereIn('name', ['order_cliente_state', 'order_online_state', 'order_siniestro_state'])
            ->where('value', 'retirar')
            ->pluck('id');

        if ($stateRetirarIds->isEmpty()) {
            $this->error('No se encontró el estado "retirar" en las tablas de estados');
            return Command::FAILURE;
        }

        $hours = 72;
        $cutoff = now()->subHours($hours);

        $orders = Order::whereIn('state_id', $stateRetirarIds)
            ->where('created_at', '<=', $cutoff)
            ->get()
            ->filter(fn($order) => !EmailLog::hasBeenSent('pedido_retirar_vencido', 'order', $order->id));

        if ($orders->isEmpty()) {
            $this->info('No hay pedidos para enviar email de vencimiento');
            return Command::SUCCESS;
        }

        $this->info("Encontrados {$orders->count()} pedidos para enviar email");

        foreach ($orders as $order) {
            if ($dryRun) {
                $this->line("DRY RUN: Se enviaría email a pedido ID {$order->id}");
            } else {
                try {
                    TraitPedidosEmail::pedidoRetirarVencido($order);
                    $this->line("✔ Email enviado a pedido ID {$order->id}");
                } catch (\Exception $e) {
                    $this->error("✘ Error al enviar email a pedido ID {$order->id}: {$e->getMessage()}");
                }
            }
        }

        $this->info('Proceso completado');
        return Command::SUCCESS;
    }
}