<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehiculoSeeder extends Seeder
{
    public function run()
    {
        $rutaArchivo = base_path('database/seeders/vehiculos.sql');
        $contenidoSQL = file_get_contents($rutaArchivo);

        DB::statement($contenidoSQL);
    }
}
