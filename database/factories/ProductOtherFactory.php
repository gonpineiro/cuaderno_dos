<?php

namespace Database\Factories;

use App\Models\Provider;
use App\Models\Table;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductOtherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => $this->faker->unique()->numberBetween(100, 652135),
            'description' => $this->faker->word(),
            'provider_id' => $this->faker->randomElement(Provider::all())['id'],
            'brand_id' => $this->faker->randomElement(Table::where('name', 'brand')->get())['id'],
        ];
    }
}
