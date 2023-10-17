<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Client;
use App\Models\Table;
use App\Models\User;
use Carbon\Carbon;
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
        $dates = [];
        for ($i = 0; $i <= 8; $i++) {
            $dates[] = Carbon::now()->subDays($i);
        }

        return [
            'user_id' =>  $this->faker->randomElement(User::all())['id'],
            'client_id' =>  $this->faker->randomElement(Client::all())['id'],

            'engine' => $this->faker->bothify('????######'),
            'chasis' => $this->faker->bothify('??#??#??#??#??#??#??#??#'),
            'information_source_id' => $this->faker->randomElement(Table::where('name', 'information_source')->get())['id'],
            'brand_id' => $this->faker->randomElement(Brand::all())['id'],
            'year' =>  $this->faker->numberBetween(1900, 2023),
            'type_price_id' => $this->faker->randomElement(Table::where('name', 'type_price')->get())['id'],
            'observation' => $this->faker->text(200),
            'created_at' => $this->faker->randomElement($dates),

            /* 'order_id' =>  $this->faker->unique()->randomElement(Order::all())['id'], */
        ];
    }
}
