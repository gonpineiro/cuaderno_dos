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
        $typeId = Table::where('name', 'order_type')->where('value', 'online')->first()['id'];

        return [
            'user_id' => $this->faker->randomElement(User::all())['id'],
            'type_id' => $typeId,
            'client_id' => $this->faker->randomElement(Client::where('is_insurance', false)->get())['id'],
            'engine' => $this->faker->bothify('????######'),
            'chasis' => $this->faker->bothify('??#??#??#??#??#??#??#??#'),
            'payment_method_id' => $this->faker->randomElement(Table::where('name', 'payment_method')->get())['id'],
            'invoice_number' => $this->faker->numberBetween(10100, 10900),
            'observation' => $this->faker->text(200),
        ];
    }
}
