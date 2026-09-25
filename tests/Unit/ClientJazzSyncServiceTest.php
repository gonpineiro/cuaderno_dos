<?php

namespace Tests\Unit;

use App\Models\Client;
use App\Models\Jazz\ClientJazz;
use App\Services\JazzServices\ClientJazzSyncService;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class ClientJazzSyncServiceTest extends TestCase
{
    /**
     * @dataProvider phoneCases
     */
    public function test_it_only_imports_a_phone_when_the_local_client_has_none($localPhone, $jazzPhone, $expected): void
    {
        $client = new Client(['phone' => $localPhone]);
        $clientJazz = new ClientJazz(['Telefono' => $jazzPhone, 'IVA_Tipo' => 99]);
        $method = new ReflectionMethod(ClientJazzSyncService::class, 'buildLocalChanges');
        $method->setAccessible(true);

        $changes = $method->invoke(new ClientJazzSyncService(), $client, $clientJazz);

        $this->assertSame($expected, $changes['phone']['to'] ?? null);
    }

    public function phoneCases(): array
    {
        return [
            'Jazz sin teléfono' => ['', '', null],
            'Jazz completa teléfono faltante' => ['', '123456', '123456'],
            'Local conserva su teléfono' => ['654321', '123456', null],
        ];
    }
}
