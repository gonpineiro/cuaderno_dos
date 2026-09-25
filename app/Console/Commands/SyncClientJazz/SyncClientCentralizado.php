<?php

namespace App\Console\Commands\SyncClientJazz;

use Illuminate\Console\Command;

class SyncClientCentralizado extends Command
{
    protected $signature = 'sync:client-centralizado
                            {--chunk=150 : Cantidad de clientes por lote para sync:client-jazz-data}
                            {--dry-run : Analiza cambios sin actualizar datos}';

    protected $description = 'Relaciona clientes con Jazz y actualiza sus datos locales';

    public function handle(): int
    {
        $options = $this->option('dry-run') ? ['--dry-run' => true] : [];

        if ($this->call('sync:client-jazz', $options) !== Command::SUCCESS) {
            $this->error('sync:client-jazz terminó con error.');

            return Command::FAILURE;
        }

        $options['--chunk'] = $this->option('chunk');

        if ($this->call('sync:client-jazz-data', $options) !== Command::SUCCESS) {
            $this->error('sync:client-jazz-data terminó con error.');

            return Command::FAILURE;
        }

        $this->info('Sincronización centralizada finalizada correctamente.');

        return Command::SUCCESS;
    }
}
