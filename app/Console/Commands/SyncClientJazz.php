<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use PDO;
use Throwable;

class SyncClientJazz extends Command
{
    private const INSERT_CHUNK_SIZE = 1000;

    protected $signature = 'sync:client-jazz
                            {--dry-run : Analiza las coincidencias sin actualizar clients}';

    protected $description = 'Relaciona clientes locales con clientes de Jazz mediante CUIT o DNI';

    public function handle(): int
    {
        $local = DB::connection('mysql');
        $jazz = DB::connection('jazz');
        $startedAt = microtime(true);
        $reportPath = null;

        try {
            $this->createTemporaryMaps($local);
            $this->createTemporaryAudit($local);
            $jazzIdentifiers = $this->loadJazzIdentifiers($jazz, $local);

            if ($this->option('dry-run')) {
                $this->loadAuditSnapshot($local);
                $stats = $this->getMatchStats($local);
                $reportPath = $this->exportAuditReport($local, false);
                $this->showStats($stats, $jazzIdentifiers, true, $startedAt, $reportPath);

                return Command::SUCCESS;
            }

            $local->beginTransaction();

            try {
                $this->loadAuditSnapshot($local);
                $stats = $this->getMatchStats($local);
                $updated = $local->affectingStatement($this->getUpdateSql());

                if ($updated !== $stats['matched']) {
                    throw new \RuntimeException(
                        "Se esperaban {$stats['matched']} actualizaciones y MySQL informó {$updated}"
                    );
                }

                $reportPath = $this->exportAuditReport($local, true);
                $local->commit();
            } catch (Throwable $e) {
                $local->rollBack();
                $this->deleteReport($reportPath);
                throw $e;
            }

            $this->showStats($stats, $jazzIdentifiers, false, $startedAt, $reportPath);
            $this->logActivity('success.syncClientJazz', array_merge($stats, [
                'report' => $reportPath,
            ]), $updated);

            return Command::SUCCESS;
        } catch (Throwable $e) {
            if ($local->transactionLevel() > 0) {
                $local->rollBack();
            }

            $this->error('No se pudo sincronizar clientes con Jazz: '.$e->getMessage());
            $this->logActivity('error.syncClientJazz', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return Command::FAILURE;
        } finally {
            try {
                $local->statement('DROP TEMPORARY TABLE IF EXISTS tmp_client_jazz_audit');
                $local->statement('DROP TEMPORARY TABLE IF EXISTS tmp_client_jazz_cuit_map');
                $local->statement('DROP TEMPORARY TABLE IF EXISTS tmp_client_jazz_dni_map');
            } catch (Throwable $e) {
                // Las tablas son temporales y MySQL las elimina al cerrar la conexión.
            }
        }
    }

    private function createTemporaryMaps(Connection $local): void
    {
        foreach (['cuit', 'dni'] as $type) {
            $table = "tmp_client_jazz_{$type}_map";

            $local->statement("DROP TEMPORARY TABLE IF EXISTS {$table}");
            $local->statement(
                "CREATE TEMPORARY TABLE {$table} (
                    identifier VARCHAR(11) NOT NULL,
                    jazz_id BIGINT UNSIGNED NOT NULL,
                    match_count INT UNSIGNED NOT NULL,
                    jazz_ids TEXT NOT NULL,
                    PRIMARY KEY (identifier)
                ) ENGINE=InnoDB
                  DEFAULT CHARSET=utf8mb4
                  COLLATE=utf8mb4_unicode_ci"
            );
        }
    }

    private function createTemporaryAudit(Connection $local): void
    {
        $local->statement('DROP TEMPORARY TABLE IF EXISTS tmp_client_jazz_audit');

        $local->statement(
            "CREATE TEMPORARY TABLE tmp_client_jazz_audit (
                client_id BIGINT UNSIGNED NOT NULL,
                name VARCHAR(255) NOT NULL,
                lastname VARCHAR(255) NULL,
                is_company TINYINT(1) NOT NULL,
                dni VARCHAR(255) NULL,
                normalized_dni VARCHAR(255) NOT NULL,
                cuit VARCHAR(255) NULL,
                normalized_cuit VARCHAR(255) NOT NULL,
                cuit_jazz_ids TEXT NULL,
                cuit_match_count INT UNSIGNED NULL,
                dni_jazz_ids TEXT NULL,
                dni_match_count INT UNSIGNED NULL,
                selected_jazz_id BIGINT UNSIGNED NULL,
                match_method VARCHAR(4) NULL,
                status VARCHAR(24) NOT NULL,
                reason VARCHAR(255) NOT NULL,
                PRIMARY KEY (client_id),
                KEY tmp_client_jazz_audit_status (status),
                KEY tmp_client_jazz_audit_selected (selected_jazz_id)
            ) ENGINE=InnoDB
              DEFAULT CHARSET=utf8mb4
              COLLATE=utf8mb4_unicode_ci"
        );
    }

    private function loadJazzIdentifiers(Connection $jazz, Connection $local): int
    {
        $statement = $jazz->getPdo()->prepare($this->getJazzIdentifiersSql());
        $statement->execute();

        $rows = [];
        $total = 0;

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = [
                $row['identifier_type'],
                $row['identifier'],
                (int) $row['jazz_id'],
                (int) $row['match_count'],
                $row['jazz_ids'],
            ];

            if (count($rows) === self::INSERT_CHUNK_SIZE) {
                $this->insertTemporaryRows($local, $rows);
                $total += count($rows);
                $rows = [];
            }
        }

        if ($rows) {
            $this->insertTemporaryRows($local, $rows);
            $total += count($rows);
        }

        $statement->closeCursor();

        return $total;
    }

    private function insertTemporaryRows(Connection $local, array $rows): void
    {
        $rowsByType = ['cuit' => [], 'dni' => []];
        foreach ($rows as $row) {
            $type = array_shift($row);
            $rowsByType[$type][] = $row;
        }

        foreach ($rowsByType as $type => $typeRows) {
            if (!$typeRows) {
                continue;
            }

            $placeholders = implode(', ', array_fill(0, count($typeRows), '(?, ?, ?, ?)'));
            $bindings = [];

            foreach ($typeRows as $row) {
                array_push($bindings, ...$row);
            }

            $local->insert(
                "INSERT INTO tmp_client_jazz_{$type}_map
                    (identifier, jazz_id, match_count, jazz_ids)
                 VALUES {$placeholders}",
                $bindings
            );
        }
    }

    private function loadAuditSnapshot(Connection $local): void
    {
        $local->statement($this->getAuditSnapshotSql());
    }

    private function getMatchStats(Connection $local): array
    {
        $row = $local->selectOne($this->getStatsSql());

        return [
            'pending' => (int) $row->pending,
            'matched' => (int) $row->matched,
            'conflicts' => (int) $row->conflicts,
            'ambiguous' => (int) $row->ambiguous,
            'without_valid_document' => (int) $row->without_valid_document,
            'without_match' => (int) $row->without_match,
        ];
    }

    private function showStats(
        array $stats,
        int $jazzIdentifiers,
        bool $dryRun,
        float $startedAt,
        string $reportPath
    ): void {
        $this->info($dryRun ? 'Análisis finalizado (sin cambios).' : 'Sincronización finalizada.');
        $this->line("Identificadores únicos cargados desde Jazz: {$jazzIdentifiers}");
        $this->line("Clientes locales pendientes: {$stats['pending']}");
        $this->line("Coincidencias válidas: {$stats['matched']}");
        $this->line("Conflictos CUIT/DNI: {$stats['conflicts']}");
        $this->line("Documentos ambiguos en Jazz: {$stats['ambiguous']}");
        $this->line("Sin documento local válido: {$stats['without_valid_document']}");
        $this->line("Sin coincidencia en Jazz: {$stats['without_match']}");

        if (!$dryRun) {
            $this->line("Clientes actualizados: {$stats['matched']}");
        }

        $this->line("Reporte de auditoría: {$reportPath}");
        $this->line('Tiempo total: '.round(microtime(true) - $startedAt, 2).' segundos');
    }

    private function exportAuditReport(Connection $local, bool $applied): string
    {
        $directory = storage_path('app/audits');

        if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
            throw new \RuntimeException("No se pudo crear el directorio de auditoría: {$directory}");
        }

        $timestamp = (new \DateTimeImmutable())->format('Ymd_His_u');
        $path = $directory.DIRECTORY_SEPARATOR."sync_client_jazz_{$timestamp}.csv";
        $handle = fopen($path, 'xb');

        if ($handle === false) {
            throw new \RuntimeException("No se pudo crear el reporte de auditoría: {$path}");
        }

        @chmod($path, 0640);

        try {
            if (fwrite($handle, "\xEF\xBB\xBF") !== 3) {
                throw new \RuntimeException('No se pudo escribir el encabezado UTF-8 del reporte');
            }

            $this->writeCsvRow($handle, [
                'client_id',
                'name',
                'lastname',
                'is_company',
                'dni',
                'normalized_dni',
                'cuit',
                'normalized_cuit',
                'cuit_jazz_ids',
                'cuit_match_count',
                'dni_jazz_ids',
                'dni_match_count',
                'selected_jazz_id',
                'match_method',
                'status',
                'reason',
            ]);

            $statement = $local->getPdo()->prepare(
                'SELECT
                    client_id,
                    name,
                    lastname,
                    is_company,
                    dni,
                    normalized_dni,
                    cuit,
                    normalized_cuit,
                    cuit_jazz_ids,
                    cuit_match_count,
                    dni_jazz_ids,
                    dni_match_count,
                    selected_jazz_id,
                    match_method,
                    status,
                    reason
                 FROM tmp_client_jazz_audit
                 ORDER BY client_id'
            );
            $statement->execute();

            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                if ($row['status'] === 'matched') {
                    $row['status'] = $applied ? 'updated' : 'would_update';
                }

                $this->writeCsvRow($handle, array_values($row));
            }

            $statement->closeCursor();

            if (!fflush($handle)) {
                throw new \RuntimeException('No se pudo completar la escritura del reporte');
            }
        } catch (Throwable $e) {
            fclose($handle);
            $this->deleteReport($path);
            throw $e;
        }

        if (!fclose($handle)) {
            $this->deleteReport($path);
            throw new \RuntimeException("No se pudo cerrar correctamente el reporte: {$path}");
        }

        return $path;
    }

    private function writeCsvRow($handle, array $row): void
    {
        $safeRow = array_map(function ($value) {
            if (is_string($value) && preg_match('/^[=+\-@\t\r]/u', $value)) {
                return "'".$value;
            }

            return $value;
        }, $row);

        if (fputcsv($handle, $safeRow, ';') === false) {
            throw new \RuntimeException('No se pudo escribir una fila del reporte de auditoría');
        }
    }

    private function deleteReport(?string $path): void
    {
        if ($path !== null && is_file($path)) {
            @unlink($path);
        }
    }

    private function logActivity(string $name, array $properties, ?int $updated = null): void
    {
        try {
            if ($updated !== null) {
                $properties['updated'] = $updated;
            }

            activity($name)
                ->withProperties($properties)
                ->log('Proceso finalizado');
        } catch (Throwable $e) {
            $this->warn('No se pudo registrar el resultado en Activity Log: '.$e->getMessage());
        }
    }

    private function getJazzIdentifiersSql(): string
    {
        return "
            SELECT
                normalized.identifier_type,
                normalized.identifier,
                MIN(normalized.jazz_id) AS jazz_id,
                COUNT(DISTINCT normalized.jazz_id) AS match_count,
                GROUP_CONCAT(
                    DISTINCT normalized.jazz_id
                    ORDER BY normalized.jazz_id
                    SEPARATOR '|'
                ) AS jazz_ids
            FROM (
                SELECT
                    'cuit' AS identifier_type,
                    REPLACE(REPLACE(REPLACE(TRIM(CUIT), '-', ''), '.', ''), ' ', '') AS identifier,
                    IdCliente AS jazz_id
                FROM clientes
                WHERE CUIT IS NOT NULL

                UNION ALL

                SELECT
                    'dni' AS identifier_type,
                    REPLACE(REPLACE(REPLACE(TRIM(NroDocumento), '-', ''), '.', ''), ' ', '') AS identifier,
                    IdCliente AS jazz_id
                FROM clientes
                WHERE NroDocumento IS NOT NULL
            ) AS normalized
            WHERE normalized.identifier REGEXP '^[0-9]+$'
              AND normalized.identifier REGEXP '[1-9]'
              AND (
                    (
                        normalized.identifier_type = 'cuit'
                        AND CHAR_LENGTH(normalized.identifier) = 11
                    )
                    OR
                    (
                        normalized.identifier_type = 'dni'
                        AND CHAR_LENGTH(normalized.identifier) BETWEEN 6 AND 8
                    )
              )
            GROUP BY normalized.identifier_type, normalized.identifier
        ";
    }

    private function getAuditSnapshotSql(): string
    {
        $candidate = $this->getCandidateJazzIdSql();
        $conflict = $this->getConflictSql();
        $ambiguous = $this->getAmbiguousSql();
        $validCuit = $this->getValidLocalCuitSql();
        $validDni = $this->getValidLocalDniSql();

        return "
            INSERT INTO tmp_client_jazz_audit (
                client_id,
                name,
                lastname,
                is_company,
                dni,
                normalized_dni,
                cuit,
                normalized_cuit,
                cuit_jazz_ids,
                cuit_match_count,
                dni_jazz_ids,
                dni_match_count,
                selected_jazz_id,
                match_method,
                status,
                reason
            )
            SELECT
                clients_local.id,
                clients_local.name,
                clients_local.lastname,
                clients_local.is_company,
                clients_local.dni,
                {$this->getNormalizedLocalDniSql()},
                clients_local.cuit,
                {$this->getNormalizedLocalCuitSql()},
                cuit_map.jazz_ids,
                cuit_map.match_count,
                dni_map.jazz_ids,
                dni_map.match_count,
                {$candidate},
                CASE
                    WHEN ({$candidate}) IS NULL THEN NULL
                    WHEN cuit_map.match_count = 1 THEN 'cuit'
                    ELSE 'dni'
                END,
                CASE
                    WHEN NOT ({$validCuit}) AND NOT ({$validDni})
                        THEN 'invalid_document'
                    WHEN {$conflict}
                        THEN 'conflict'
                    WHEN {$ambiguous}
                        THEN 'ambiguous'
                    WHEN ({$candidate}) IS NOT NULL
                        THEN 'matched'
                    ELSE 'not_found'
                END,
                CASE
                    WHEN NOT ({$validCuit}) AND NOT ({$validDni})
                        THEN 'El cliente no tiene CUIT ni DNI válido'
                    WHEN {$conflict}
                        THEN 'CUIT y DNI apuntan a distintos IdCliente'
                    WHEN cuit_map.match_count > 1
                        THEN 'El CUIT coincide con varios clientes de Jazz'
                    WHEN cuit_map.match_count IS NULL AND dni_map.match_count > 1
                        THEN 'El DNI coincide con varios clientes de Jazz'
                    WHEN ({$candidate}) IS NOT NULL AND cuit_map.match_count = 1
                        THEN 'Coincidencia única por CUIT'
                    WHEN ({$candidate}) IS NOT NULL AND dni_map.match_count = 1
                        THEN 'Coincidencia única por DNI'
                    ELSE 'No existe coincidencia en Jazz'
                END
            FROM clients AS clients_local
            {$this->getMapJoinsSql()}
            WHERE clients_local.jazz_id IS NULL
              AND clients_local.deleted_at IS NULL
        ";
    }

    private function getStatsSql(): string
    {
        return "
            SELECT
                COUNT(*) AS pending,
                COALESCE(SUM(status = 'matched'), 0) AS matched,
                COALESCE(SUM(status = 'conflict'), 0) AS conflicts,
                COALESCE(SUM(status = 'ambiguous'), 0) AS ambiguous,
                COALESCE(SUM(status = 'invalid_document'), 0) AS without_valid_document,
                COALESCE(SUM(status = 'not_found'), 0) AS without_match
            FROM tmp_client_jazz_audit
        ";
    }

    private function getUpdateSql(): string
    {
        return "
            UPDATE clients AS clients_local
            INNER JOIN tmp_client_jazz_audit AS audit
                ON audit.client_id = clients_local.id
            SET
                clients_local.jazz_id = audit.selected_jazz_id,
                clients_local.updated_at = CURRENT_TIMESTAMP
            WHERE clients_local.jazz_id IS NULL
              AND clients_local.deleted_at IS NULL
              AND audit.status = 'matched'
              AND audit.selected_jazz_id IS NOT NULL
        ";
    }

    private function getMapJoinsSql(): string
    {
        return "
            LEFT JOIN tmp_client_jazz_cuit_map AS cuit_map
                ON cuit_map.identifier = {$this->getNormalizedLocalCuitSql()}
            LEFT JOIN tmp_client_jazz_dni_map AS dni_map
                ON dni_map.identifier = {$this->getNormalizedLocalDniSql()}
        ";
    }

    private function getCandidateJazzIdSql(): string
    {
        return "
            CASE
                WHEN cuit_map.match_count > 1 THEN NULL
                WHEN {$this->getConflictSql()} THEN NULL
                WHEN cuit_map.match_count = 1 THEN cuit_map.jazz_id
                WHEN dni_map.match_count = 1 THEN dni_map.jazz_id
                ELSE NULL
            END
        ";
    }

    private function getConflictSql(): string
    {
        return 'COALESCE((
            cuit_map.match_count = 1
            AND dni_map.match_count = 1
            AND cuit_map.jazz_id <> dni_map.jazz_id
        ), 0)';
    }

    private function getAmbiguousSql(): string
    {
        return 'COALESCE((
            cuit_map.match_count > 1
            OR (
                cuit_map.match_count IS NULL
                AND dni_map.match_count > 1
            )
        ), 0)';
    }

    private function getValidLocalCuitSql(): string
    {
        $cuit = $this->getNormalizedLocalCuitSql();

        return "(
            {$cuit} REGEXP '^[0-9]+$'
            AND CHAR_LENGTH({$cuit}) = 11
            AND {$cuit} REGEXP '[1-9]'
        )";
    }

    private function getValidLocalDniSql(): string
    {
        $dni = $this->getNormalizedLocalDniSql();

        return "(
            {$dni} REGEXP '^[0-9]+$'
            AND CHAR_LENGTH({$dni}) BETWEEN 6 AND 8
            AND {$dni} REGEXP '[1-9]'
        )";
    }

    private function getNormalizedLocalCuitSql(): string
    {
        return "REPLACE(
            REPLACE(
                REPLACE(TRIM(COALESCE(clients_local.cuit, '')), '-', ''),
                '.',
                ''
            ),
            ' ',
            ''
        )";
    }

    private function getNormalizedLocalDniSql(): string
    {
        return "REPLACE(
            REPLACE(
                REPLACE(TRIM(COALESCE(clients_local.dni, '')), '-', ''),
                '.',
                ''
            ),
            ' ',
            ''
        )";
    }
}
