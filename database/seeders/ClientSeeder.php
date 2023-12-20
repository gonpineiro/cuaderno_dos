<?php

namespace Database\Seeders;

use App\Models\Client;
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
        $generic = [
            'dni' => '99999999',
            'name' => 'Allende Repuestos',
            'email' => 'allende@allende.com.ar',
            'is_company' => false,
            'is_insurance' => false,
            'is_generic' => true,
        ];
        Client::factory()->create($generic);
        Client::factory()->count(50)->create();
    }
}
