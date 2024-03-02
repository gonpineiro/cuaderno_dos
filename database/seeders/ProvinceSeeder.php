<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinceSeeder extends Seeder
{

    public function run()
    {
        $rutaArchivo = base_path('database/seeders/provinces.sql');
        $contenidoSQL = file_get_contents($rutaArchivo);

        DB::statement($contenidoSQL);
    }
}
