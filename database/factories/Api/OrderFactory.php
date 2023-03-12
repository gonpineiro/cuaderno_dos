<?php

namespace Database\Factories\Api;

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
            'detalle' => $this->faker->word(),
        ];
    }
}
