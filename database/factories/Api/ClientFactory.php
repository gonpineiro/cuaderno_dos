<?php

namespace Database\Factories\Api;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'dni' => $this->faker->unique()->numberBetween(20000000, 48000000),
            'name' => $this->faker->name(),
            'phone' => $this->faker->unique()->phoneNumber(),
            'email' => $this->faker->email(),
            'city' => $this->faker->city(),
            'adress' => $this->faker->address(),
            'cuit' => $this->faker->unique()->numberBetween(10000000001, 30000000009),
            'is_company' => $this->faker->boolean(),
        ];
    }
}
