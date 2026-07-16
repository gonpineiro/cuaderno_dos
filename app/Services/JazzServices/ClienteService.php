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

    public function modificarCliente(Client $cliente)
    {
        $idCliente = (int) $cliente->jazz_id;

        if (!$idCliente) {
            throw new \Exception('El cliente no tiene jazz_id para modificar en Jazz');
        }

        $payload = $this->buildPayload($cliente, $idCliente);

        return $this->post('Cliente/ModificarCliente', $payload);
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
            /* 'cuit' => $this->getClientCuit($cliente), */
            'mail' => $this->normalizeString($cliente->email),
            'telefono' => $this->normalizeString($cliente->phone),
            'telParticular' => '',
            'telCelular' => '',
            'fax' => '',
            'permitirFacturarenctacte' => true,
            'fechaNacimiento' => now()->format('d/m/Y'),
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
        $cuit = preg_replace('/\D+/', '', (string) $cliente->cuit);

        if (strlen($cuit) === 11) {
            return substr($cuit, 0, 2) . '-' . substr($cuit, 2, 8) . '-' . substr($cuit, 10, 1);
        }

        $dni = preg_replace('/\D+/', '', (string) $cliente->dni);

        if (strlen($dni) === 8) {
            $generatedCuit = (string) generateCuit($dni);

            if (strlen($generatedCuit) === 11) {
                return substr($generatedCuit, 0, 2) . '-' . substr($generatedCuit, 2, 8) . '-' . substr($generatedCuit, 10, 1);
            }
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
