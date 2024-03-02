<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin'),
        ]);

        DB::table('users')->insert([
            'name' => 'Número uno',
            'email' => 'u@u.com',
            'password' => Hash::make('u'),
        ]);

        DB::table('users')->insert([
            'name' => 'Allende',
            'email' => 'allende@allende.com.ar',
            'password' => Hash::make('admin'),
        ]);

        $this->call([
            BrandSeeder::class,
            TableSeeder::class,
            ProvinceSeeder::class,
            CitySeeder::class,
            ProviderSeeder::class,
            ClientSeeder::class,
            VehiculoSeeder::class,
            CoeficientesSeeder::class,
            /* ProductSeeder::class, */
            /* PriceQuoteSeeder::class, */
            /* OrderSeeder::class, */
            /* SiniestroSeeder::class, */
        ]);
    }
}
