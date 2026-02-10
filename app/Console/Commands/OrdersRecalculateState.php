<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;

class OrdersRecalculateState extends Command
{
    protected $signature = 'orders:recalculate-state {--chunk=500}';
    protected $description = 'Recalcula y persiste el estado general de todas las órdenes';

    public function handle(): int
    {
        $chunk = (int) $this->option('chunk');

        $this->info('Recalculando estados de órdenes...');

        Order::withTrashed()
            ->with(['detail.state', 'type', 'shipment'])
            ->whereNull('deleted_at')
            ->chunkById($chunk, function ($orders) {
                foreach ($orders as $order) {
                    $state = $order->getGeneralState();

                    if ($state && isset($state->id)) {
                        $order->update([
                            'state_id' => $state->id,
                        ]);
                    }
                }
            });

        $this->info('✔ Estados recalculados correctamente');

        return Command::SUCCESS;
    }
}
