<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SiniestroFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $typeId = Table::where('name', 'order_type')->where('value', 'siniestro')->first()['id'];

        return [
            'user_id' => $this->faker->randomElement(User::all())['id'],
            'type_id' => $typeId,
            'client_id' => $this->faker->randomElement(Client::where('is_insurance', true)->get())['id'],
            'engine' => $this->faker->bothify('????######'),
            'chasis' => $this->faker->bothify('??#??#??#??#??#??#??#??#'),
            'remito' => $this->faker->numberBetween(10100, 10900),
            'workshop' => $this->faker->text(5),
            'observation' => $this->faker->text(200),
        ];
    }
}
