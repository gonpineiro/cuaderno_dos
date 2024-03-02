<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    public function run()
    {
        $rutaArchivo = base_path('database/seeders/cities.sql');
        $contenidoSQL = file_get_contents($rutaArchivo);

        DB::statement($contenidoSQL);
    }
}
