<?php

namespace Database\Factories\Api;

use App\Models\Api\Client;
use App\Models\Api\Table;
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
        return [
            'user_id' =>  $this->faker->randomElement(User::all())['id'],
            'type_id' =>  $this->faker->randomElement(Table::where('name', 'order_type')->get())['id'],
            'client_id' =>  $this->faker->randomElement(Client::all())['id'],

            'engine' => $this->faker->bothify('????######'),
            'chasis' => $this->faker->bothify('??#??#??#??#??#??#??#??#'),
            'payment_method' => $this->faker->randomElement(['Pago en mostrador', 'Pagado online', 'Cuenta corriente']),
            'invoice_number' => $this->faker->numberBetween(10100, 10900),
            'observation' => $this->faker->text(200),
        ];
    }
}
