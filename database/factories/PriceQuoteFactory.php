<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PriceQuoteFactory extends Factory
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
            'client_id' =>  $this->faker->randomElement(Client::all())['id'],

            'engine' => $this->faker->bothify('????######'),
            'chasis' => $this->faker->bothify('??#??#??#??#??#??#??#??#'),
            'information_source' => $this->faker->randomElement(['WhatsApp Clientes', 'WhatsApp Mecanicos', 'Facebook', 'Mail', 'Google', 'Pagina Web']),
            'type_price' => $this->faker->randomElement(['contado', 'lista']),
            'observation' => $this->faker->text(200),

            'order_id' =>  $this->faker->unique()->randomElement(Order::all())['id'],
        ];
    }
}
