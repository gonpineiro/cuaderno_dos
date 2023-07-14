<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $typeId = $this->faker->randomElement(Table::where('name', 'order_type')->get())['id'];

        $paymentMethod = null;
        $invoiceNumber = null;
        $remito = null;
        $workshop = null;

        if ($typeId === 6) {
            $paymentMethod = $this->faker->randomElement(['Pago en mostrador', 'Pagado online', 'Cuenta corriente']);
            $invoiceNumber = $this->faker->numberBetween(10100, 10900);
        }

        if ($typeId === 8) {
            $remito = $this->faker->numberBetween(10100, 10900);
            $workshop = $this->faker->text(5);
        }

        return [
            'user_id' => $this->faker->randomElement(User::all())['id'],
            'type_id' => $typeId,
            'client_id' => $this->faker->randomElement(Client::all())['id'],
            'engine' => $this->faker->bothify('????######'),
            'chasis' => $this->faker->bothify('??#??#??#??#??#??#??#??#'),
            'payment_method' => $paymentMethod,
            'invoice_number' => $invoiceNumber,
            'remito' => $remito,
            'workshop' => $workshop,
            'observation' => $this->faker->text(200),
        ];
    }
}
