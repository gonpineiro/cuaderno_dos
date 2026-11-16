<?php

namespace Tests\Unit;

use App\Http\Resources\Order\OrderResource;
use App\Models\Client;
use App\Models\Order;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Tests\TestCase;

class OrderResourceTest extends TestCase
{
    public function test_it_uses_payment_methods_from_the_observation_when_no_single_method_exists(): void
    {
        $order = new Order;
        $order->setRawAttributes([
            'id' => 19485,
            'observation' => 'TENEMOS STOCK@CONTADO / DEB / 1 CUOTA: $30000;6 CUOTAS NARANJA: $127250',
            'created_at' => '2026-11-16 00:00:00',
        ]);
        $order->setRelation('user', new User(['name' => 'Usuario']));
        $order->setRelation('client', new Client(['name' => 'Cliente']));
        $order->setRelation('vehiculo', new Vehiculo(['name' => 'Vehículo']));
        $order->setRelation('shipment', null);
        $order->setRelation('payment_method', null);

        $data = (new OrderResource($order))->resolve(Request::create('/'));

        $this->assertSame(
            'CONTADO / DEB / 1 CUOTA, 6 CUOTAS NARANJA',
            $data['payment_method'],
        );
    }
}
