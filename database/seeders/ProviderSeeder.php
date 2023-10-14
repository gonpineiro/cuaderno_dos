<?php

namespace Database\Seeders;

use App\Models\Provider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rutaArchivo = base_path('database/seeders/providers.sql');
        $contenidoSQL = file_get_contents($rutaArchivo);

        DB::statement($contenidoSQL);
    }
}
