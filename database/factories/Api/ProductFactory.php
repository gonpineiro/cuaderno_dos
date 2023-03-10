<?php

namespace Database\Factories\Api;

use App\Models\Api\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'provider_id' => $this->faker->randomElement(Provider::all())['id'],
        ];
    }
}
