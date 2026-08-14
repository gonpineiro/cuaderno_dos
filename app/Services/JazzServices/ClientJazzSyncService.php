<?php

namespace App\Services\JazzServices;

use App\Models\Client;
use App\Models\City;
use App\Models\ClientConfig;
use App\Models\Jazz\ClientJazz;
use App\Models\Table;
use Illuminate\Support\Collection;

class ClientJazzSyncService
{
    private const IVA_TIPO_TO_VALUE = [
        0 => 'resp_incripto',
        2 => 'exento',
        3 => 'cons_final',
        4 => 'resp_monotributo',
    ];

    private ?Collection $condicionIvaMap = null;
    private ?Collection $cityMap = null;

    public function syncJazzFromLocal(Client $client, ?ClientJazz $clientJazz = null, bool $persist = true): array
    {
        if (!$client->jazz_id && !$clientJazz) {
            return [
                'jazz_changes' => [],
                'saved' => false,
            ];
        }

        $client->loadMissing('config', 'condicion_iva');
        $clientJazz = $clientJazz ?: ClientJazz::findOrFail($client->jazz_id);

        $changes = [
            'NroDocumento' => $this->normalizeDni($client->dni),
            'IVA_Tipo' => (new ClienteService())->getIvaTipo($client),
        ];

        $hasCuentaCorrienteConfig = $client->config->contains(function ($config) {
            return !is_null($config->es_cuenta_corriente);
        });

        if ($hasCuentaCorrienteConfig) {
            $changes['PermitirFacturarenctacte'] = $client->config->contains(function ($config) {
                return (int) $config->es_cuenta_corriente === 1;
            }) ? 'S' : 'N';
        }

        $dirty = [];

        foreach ($changes as $field => $value) {
            if ($clientJazz->{$field} !== $value) {
                $dirty[$field] = [
                    'from' => $clientJazz->{$field},
                    'to' => $value,
                ];
                $clientJazz->{$field} = $value;
            }
        }

        if ($persist && !empty($dirty)) {
            $clientJazz->save();
        }

        return [
            'jazz_changes' => $dirty,
            'saved' => $persist && !empty($dirty),
        ];
    }

    public function syncLocalFromJazz(Client $client, ?ClientJazz $clientJazz = null, bool $persist = true): array
    {
        if (!$client->jazz_id && !$clientJazz) {
            return [
                'client_changes' => [],
                'config_changes' => [],
                'saved' => false,
            ];
        }

        $client->loadMissing('config', 'city');
        $clientJazz = $clientJazz ?: ClientJazz::findOrFail($client->jazz_id);

        $clientChanges = $this->buildLocalChanges($client, $clientJazz);
        $configChanges = $this->buildConfigChanges($client, $clientJazz);
        $saved = false;

        if ($persist) {
            foreach ($clientChanges as $attribute => $change) {
                $client->{$attribute} = $change['to'];
            }

            if (!empty($clientChanges)) {
                $client->save();
                $saved = true;
            }

            foreach ($configChanges as $configChange) {
                $config = $client->config->firstWhere('id', $configChange['id']);

                if ($config) {
                    $config->es_cuenta_corriente = $configChange['to'];
                    $config->save();
                    $saved = true;
                }
            }
        }

        return [
            'client_changes' => $clientChanges,
            'config_changes' => $configChanges,
            'saved' => $saved,
        ];
    }

    private function buildLocalChanges(Client $client, ClientJazz $clientJazz): array
    {
        $desired = [];
        $nameParts = $this->parseJazzName($clientJazz->Nombre);

        $this->setIfNotBlank($desired, 'name', $nameParts['name'] ?? null);
        $this->setIfKeyExists($desired, 'lastname', $nameParts);
        $this->setIfNotBlank($desired, 'adress', $this->normalizeText($clientJazz->Domicilio, 255));
        $this->setIfNotBlank($desired, 'phone', $this->extractPhone($clientJazz));
        $this->setIfNotBlank($desired, 'email', $this->extractMail($clientJazz));
        $this->setIfNotBlank($desired, 'dni', $this->normalizeJazzDocument($clientJazz->NroDocumento));
        $this->setIfNotBlank($desired, 'cuit', $this->normalizeCuit($clientJazz->CUIT));

        $condicionIvaId = $this->resolveCondicionIvaId($clientJazz->IVA_Tipo);
        if ($condicionIvaId !== null) {
            $desired['condicion_iva_id'] = $condicionIvaId;
        }

        $cityId = $this->resolveCityId($clientJazz->Localidad);
        if ($cityId !== null) {
            $desired['city_id'] = $cityId;
        }

        $changes = [];

        foreach ($desired as $attribute => $newValue) {
            $currentValue = $client->{$attribute};

            if ((string) $currentValue !== (string) $newValue) {
                $changes[$attribute] = [
                    'from' => $currentValue,
                    'to' => $newValue,
                ];
            }
        }

        return $changes;
    }

    private function buildConfigChanges(Client $client, ClientJazz $clientJazz): array
    {
        $allowCuentaCorriente = $this->normalizeCuentaCorrienteFlag($clientJazz->PermitirFacturarenctacte);

        if ($allowCuentaCorriente === null) {
            return [];
        }

        return $client->config
            ->filter(function (ClientConfig $config) use ($allowCuentaCorriente) {
                return is_null($config->es_cuenta_corriente)
                    || (int) $config->es_cuenta_corriente !== $allowCuentaCorriente;
            })
            ->map(function (ClientConfig $config) use ($allowCuentaCorriente) {
                return [
                    'id' => $config->id,
                    'type' => $config->type,
                    'from' => $config->es_cuenta_corriente,
                    'to' => $allowCuentaCorriente,
                ];
            })
            ->values()
            ->all();
    }

    private function parseJazzName($value): array
    {
        $name = $this->normalizeText($value, 255);

        if ($name === '') {
            return [];
        }

        if (mb_strpos($name, ',') !== false) {
            [$lastname, $firstname] = array_map('trim', explode(',', $name, 2));

            return [
                'name' => $this->normalizeText($firstname, 35),
                'lastname' => $this->normalizeText($lastname, 35),
            ];
        }

        return [
            'name' => $this->normalizeText($name, 35),
            'lastname' => null,
        ];
    }

    private function extractPhone(ClientJazz $clientJazz): string
    {
        foreach ([$clientJazz->Telefono, $clientJazz->TelCelular, $clientJazz->TelParticular] as $phone) {
            $normalized = $this->normalizeText($phone, 30);

            if ($normalized !== '') {
                return $normalized;
            }
        }

        return '';
    }

    private function extractMail(ClientJazz $clientJazz): string
    {
        foreach ([$clientJazz->Mail, $clientJazz->Mail2] as $mail) {
            $normalized = $this->normalizeText($mail, 100);

            if ($normalized !== '' && filter_var($normalized, FILTER_VALIDATE_EMAIL)) {
                return $normalized;
            }
        }

        return '';
    }

    private function resolveCondicionIvaId($ivaTipo): ?int
    {
        $value = self::IVA_TIPO_TO_VALUE[(int) $ivaTipo] ?? null;

        if ($value === null) {
            return null;
        }

        if ($this->condicionIvaMap === null) {
            $this->condicionIvaMap = Table::query()
                ->where('name', 'client_condicion_iva')
                ->pluck('id', 'value');
        }

        $id = $this->condicionIvaMap->get($value);

        return $id !== null ? (int) $id : null;
    }

    private function resolveCityId($localidad): ?int
    {
        $normalized = $this->normalizeLookupValue($localidad);

        if ($normalized === '') {
            return null;
        }

        if ($this->cityMap === null) {
            $this->cityMap = City::query()
                ->select('id', 'name')
                ->get()
                ->groupBy(function (City $city) {
                    return $this->normalizeLookupValue($city->name);
                });
        }

        $cities = $this->cityMap->get($normalized);

        if (!$cities || $cities->count() !== 1) {
            return null;
        }

        return (int) $cities->first()->id;
    }

    private function normalizeJazzDocument($value): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $value);

        if (strlen($digits) < 6 || strlen($digits) > 8) {
            return null;
        }

        return $digits;
    }

    private function normalizeDni($value): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $value);

        if ($digits === '') {
            return null;
        }

        return $digits;
    }

    private function normalizeCuit($value): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $value);

        if (strlen($digits) !== 11) {
            return null;
        }

        return $digits;
    }

    private function normalizeCuentaCorrienteFlag($value): ?int
    {
        $normalized = mb_strtoupper(trim((string) $value));

        if ($normalized === 'S') {
            return 1;
        }

        if ($normalized === 'N') {
            return 0;
        }

        return null;
    }

    private function normalizeText($value, int $maxLength): string
    {
        $value = trim(preg_replace('/\s+/u', ' ', (string) $value));

        if ($value === '') {
            return '';
        }

        return mb_substr($value, 0, $maxLength);
    }

    private function normalizeLookupValue($value): string
    {
        $value = $this->normalizeText($value, 255);

        return mb_strtolower($value);
    }

    private function setIfNotBlank(array &$attributes, string $key, ?string $value): void
    {
        if ($value !== null && $value !== '') {
            $attributes[$key] = $value;
        }
    }

    private function setIfKeyExists(array &$attributes, string $key, array $values): void
    {
        if (array_key_exists($key, $values)) {
            $attributes[$key] = $values[$key];
        }
    }
}
