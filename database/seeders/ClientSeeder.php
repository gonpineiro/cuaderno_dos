<?php

namespace Database\Seeders;

use App\Models\Api\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Client::factory()->count(200)->create();
    }
}
