<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Jazz\ClientJazz;
use App\Services\JazzServices\ClientJazzSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class SyncClientJazzData extends Command
{
    protected $signature = 'sync:client-jazz-data
                            {--client-id=* : IDs locales de clientes a sincronizar}
                            {--chunk=100 : Cantidad de clientes por lote}
                            {--dry-run : Analiza cambios sin actualizar datos locales}';

    protected $description = 'Actualiza clientes locales asociados con la informacion disponible en Jazz';

    public function handle(ClientJazzSyncService $syncService): int
    {
        $chunk = max(1, (int) $this->option('chunk'));
        $clientIds = array_filter(array_map('intval', (array) $this->option('client-id')));
        $dryRun = (bool) $this->option('dry-run');
        $stats = [
            'processed' => 0,
            'updated' => 0,
            'unchanged' => 0,
            'missing_in_jazz' => 0,
            'errors' => 0,
        ];

        $query = Client::query()
            ->whereNotNull('jazz_id')
            ->whereNull('deleted_at')
            ->with('config');

        if (!empty($clientIds)) {
            $query->whereIn('id', $clientIds);
        }

        $this->info($dryRun ? 'Analizando clientes asociados con Jazz...' : 'Sincronizando clientes asociados con Jazz...');

        $query->orderBy('id')->chunkById($chunk, function ($clients) use ($syncService, $dryRun, &$stats) {
            foreach ($clients as $client) {
                $stats['processed']++;

                try {
                    $clientJazz = ClientJazz::find($client->jazz_id);

                    if (!$clientJazz) {
                        $stats['missing_in_jazz']++;
                        $this->warn("Cliente {$client->id}: no existe IdCliente {$client->jazz_id} en Jazz.");
                        continue;
                    }

                    $result = DB::transaction(function () use ($syncService, $client, $clientJazz, $dryRun) {
                        return $syncService->syncLocalFromJazz($client, $clientJazz, !$dryRun);
                    });

                    $changeCount = count($result['client_changes']) + count($result['config_changes']);

                    if ($changeCount === 0) {
                        $stats['unchanged']++;
                        $this->line("Cliente {$client->id}: sin cambios.");
                        continue;
                    }

                    $stats['updated']++;

                    $mode = $dryRun ? 'cambios detectados' : 'actualizado';
                    $this->line("Cliente {$client->id}: {$mode} ({$changeCount} cambios).");
                } catch (Throwable $e) {
                    $stats['errors']++;
                    $this->error("Cliente {$client->id}: ".$e->getMessage());
                }
            }
        });

        $this->newLine();
        $this->info($dryRun ? 'Analisis finalizado.' : 'Sincronizacion finalizada.');
        $this->line("Procesados: {$stats['processed']}");
        $this->line($dryRun ? "Con cambios: {$stats['updated']}" : "Actualizados: {$stats['updated']}");
        $this->line("Sin cambios: {$stats['unchanged']}");
        $this->line("Sin registro en Jazz: {$stats['missing_in_jazz']}");
        $this->line("Errores: {$stats['errors']}");

        return $stats['errors'] > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
