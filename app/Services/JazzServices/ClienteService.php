<?php

namespace App\Services\JazzServices;

use App\Models\Client;
use Illuminate\Support\Facades\DB;

class ClienteService extends ApiService
{
    public function agregarCliente(Client $cliente)
    {
        $initialIdCliente = $this->getNextIdCliente();
        $maxRetries = 5;

        for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
            $idCliente = $initialIdCliente + $attempt;
            $payload = $this->buildPayload($cliente, $idCliente);
            $response = $this->post('Cliente/Create', $payload);

            if (!isset($response['hasErrorMessage']) || !$response['hasErrorMessage']) {
                return $idCliente;
            }

            $message = $response['responseMessage'] ?? 'No se logro crear el cliente en Jazz';

            if (!$this->shouldRetryWithNextId($message)) {
                throw new \Exception($message);
            }
        }

        throw new \Exception('No se logro encontrar un idCliente libre en Jazz luego de varios intentos');
    }

    public function obtenerCliente(int $idCliente)
    {
        return $this->get("Cliente/ObtenerCliente/{$idCliente}");
    }

    protected function getNextIdCliente(): int
    {
        $lastId = DB::connection('jazz')
            ->table('clientes')
            ->max('IdCliente');

        return ((int) $lastId) + 1;
    }

    protected function buildPayload(Client $cliente, int $idCliente): array
    {
        return [
            'idCliente' => $idCliente,
            'nombre' => $this->getClientName($cliente),
            'empresa' => 1,
            'domicilio' => $this->normalizeString($cliente->adress),
            'numero' => (string) $idCliente,
            'cp' => '',
            'localidad' => $this->normalizeString(optional($cliente->city)->name),
            'ivaTipo' => 0,
            'obs' => $this->normalizeString($cliente->observation),
            'cuit' => $this->getClientCuit($cliente),
            'mail' => $this->normalizeString($cliente->email),
            'telefono' => $this->normalizeString($cliente->phone),
            'telParticular' => '',
            'telCelular' => '',
            'fax' => '',
            'descuentoHabitual' => '0',
            'activo' => 'A',
            'limiteCred' => 0,
            'isib' => '0',
            'telefonoDeposito' => '',
            'domicilioDeposito' => '',
            'horarioDeposito' => '',
            'exentoIibb' => 'N',
            'esInscrGcias' => 0,
            'afectaInfoProntoPago' => 'N',
            'carpetaRelacionada' => '',
            'exganDesde' => '',
            'exganHasta' => '',
            'reservado' => 'S',
            'idPais' => 6,
        ];
    }

    protected function shouldRetryWithNextId(string $message): bool
    {
        return str_contains(
            mb_strtolower($message),
            mb_strtolower('El id que quiere asignar ya existe')
        );
    }

    protected function getClientName(Client $cliente): string
    {
        $lastname = $this->normalizeString($cliente->lastname ?? '');
        $name = $this->normalizeString($cliente->name);

        if ($lastname !== '' && $name !== '') {
            return "{$lastname}, {$name}";
        }

        return $lastname !== '' ? $lastname : $name;
    }

    protected function getClientCuit(Client $cliente): string
    {
        $dni = preg_replace('/\D+/', '', (string) $cliente->dni);

        if (strlen($dni) === 11) {
            return $dni;
        }

        if (strlen($dni) === 8) {
            return (string) generateCuit($dni);
        }

        return '';
    }

    protected function normalizeString($value): string
    {
        if ($value === null) {
            return '';
        }

        $value = trim((string) $value);

        return $value === '' ? '' : $value;
    }
}
