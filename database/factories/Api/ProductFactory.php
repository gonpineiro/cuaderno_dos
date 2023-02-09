<?php

namespace Database\Factories\Api;

use App\Models\Api\Provider;
use App\Models\Api\Table;

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
            'code' => $this->faker->randomNumber(5, true),
            'factory_code' => $this->faker->randomNumber(5, true),
            'equivalence' => $this->faker->randomNumber(5, true),

            'description' => $this->faker->word(),
            'model' => $this->faker->word(),
            'engine' => $this->faker->word(),
            'observation' => $this->faker->sentence(3),

            'min_stock' => $this->faker->boolean(),
            'empty_stock' => $this->faker->boolean(),

            /*  Configuracion global */
            'ship' => 1,                                        /* DD */
            'module' => $this->faker->randomDigitNotNull(),     /* DD */
            /* - */
            'side' => 'D'
            /** F A I D */
            ,
            'column' => $this->faker->randomDigitNotNull(),     /* 0D */
            'row' => $this->faker->randomDigitNotNull(),        /* 0D */

            'provider_id' => $this->faker->randomElement(Provider::all())['id'],
            'brand_id' => $this->faker->randomElement(Table::where('name', 'brand')->get())['id'],
        ];
    }
}
