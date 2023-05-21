<?php

namespace Database\Factories;

use App\Models\Api\Client;
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
            'type_price' => $this->faker->randomElement(['Precio de contado', 'Precio de lista']),
            'observation' => $this->faker->text(200),
        ];
    }
}
