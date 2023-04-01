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

        /* DB::table('users')->insert([
            'name' => 'Número uno',
            'email' => 'u@u.com',
            'password' => Hash::make('u'),
        ]); */

        $this->call([
            TableSeeder::class,
            ProviderSeeder::class,
            ClientSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
