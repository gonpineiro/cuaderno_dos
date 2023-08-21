<?php

namespace Database\Factories;

use App\Models\Provider;
use App\Models\Table;

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
        $code = $this->faker->unique()->numberBetween(100, 652135);
        if ($in_catalogue = $this->faker->boolean()) {
            return [
                'code' => $code,
                'provider_code' => $this->faker->randomNumber(5, true),
                'factory_code' => $this->faker->randomNumber(5, true),
                'equivalence' => $this->faker->randomNumber(5, true),

                'description' => $this->faker->text(200),
                'model' => $this->faker->word(),
                'engine' => $this->faker->bothify('????######'),
                'observation' => $this->faker->sentence(3),

                'min_stock' => $this->faker->boolean(),
                'empty_stock' => $this->faker->boolean(),

                'ship' => 1,
                'module' => $this->faker->randomDigitNotNull(),
                'side' => 'D',
                'column' => $this->faker->randomDigitNotNull(),
                'row' => $this->faker->randomDigitNotNull(),

                'in_catalogue' => $in_catalogue,

                'provider_id' => $this->faker->randomElement(Provider::all())['id'],
                'brand_id' => $this->faker->randomElement(Table::where('name', 'brand')->get())['id'],
            ];
        } else if ($is_special = $this->faker->boolean()) {
            return [
                'code' => $code,
                'description' => $this->faker->text(200),
                'is_special' => $is_special,
            ];
        } else {    
            return [
                'code' => $code,
                'description' => $this->faker->text(200),
                'provider_id' => $this->faker->randomElement(Provider::all())['id'],
            ];
        }
    }
}
