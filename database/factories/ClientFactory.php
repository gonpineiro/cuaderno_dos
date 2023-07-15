<?php

namespace Database\Factories;

use App\Models\City;
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
        $is_insurance = $this->faker->boolean();
        $is_company = $this->faker->boolean();

        if ($is_insurance) {
            return [
                'name' => $this->faker->company(),
                'cuit' => $this->faker->unique()->numberBetween(10000000001, 30000000009),
                'is_insurance' => $is_insurance,
            ];
        } else if ($is_company) {
            return [
                'name' => $this->faker->name(),
                'phone' => $this->faker->unique()->phoneNumber(),
                'email' => $this->faker->email(),
                'city_id' =>  $this->faker->randomElement(City::all())['id'],
                'adress' => $this->faker->address(),
                'cuit' => $this->faker->unique()->numberBetween(10000000001, 30000000009),
                'is_company' => $is_company,
            ];
        } else {
            return [
                'dni' => $this->faker->unique()->numberBetween(20000000, 48000000),
                'name' => $this->faker->name(),
                'phone' => $this->faker->unique()->phoneNumber(),
                'email' => $this->faker->email(),
                'city_id' =>  $this->faker->randomElement(City::all())['id'],
                'adress' => $this->faker->address(),
            ];
        }
    }
}
