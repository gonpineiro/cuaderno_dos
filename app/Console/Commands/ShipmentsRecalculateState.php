<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Shipment;

class ShipmentsRecalculateState extends Command
{
    protected $signature = 'shipment:recalculate-state {--chunk=500}';
    protected $description = 'Recalcula y persiste el estado general de todas los envíos';

    public function handle(): int
    {
        $chunk = (int) $this->option('chunk');

        $this->info('Recalculando estados de envíos...');

        Shipment::with(['detail.state'])
            ->chunkById($chunk, function ($shipments) {
                foreach ($shipments as $shipment) {
                    $state = $shipment->getGeneralState();

                    if ($state && isset($state->id)) {
                        $shipment->update([
                            'state_id' => $state->id,
                        ]);
                    }
                }
            });

        $this->info('✔ Estados recalculados correctamente');

        return Command::SUCCESS;
    }
}
