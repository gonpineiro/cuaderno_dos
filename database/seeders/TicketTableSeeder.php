<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TicketTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tables')->insert(['name' => 'ticket_estado', 'value' => 'abierto', 'description' => 'Abierto', 'background_color' => '#dc3545', 'color' => 'white']);
        DB::table('tables')->insert(['name' => 'ticket_estado', 'value' => 'en_progreso', 'description' => 'En proceso', 'background_color' => '#ffc107', 'color' => 'black']);
        DB::table('tables')->insert(['name' => 'ticket_estado', 'value' => 'cerrado', 'description' => 'Cerrado', 'background_color' => '#198754', 'color' => 'white']);

        DB::table('tables')->insert(['name' => 'ticket_prioridad', 'value' => 'baja', 'description' => 'Baja', 'background_color' => '#198754', 'color' => 'white']);
        DB::table('tables')->insert(['name' => 'ticket_prioridad', 'value' => 'media', 'description' => 'Media', 'background_color' => '#0d6efd', 'color' => 'white']);
        DB::table('tables')->insert(['name' => 'ticket_prioridad', 'value' => 'alta', 'description' => 'Alta', 'background_color' => '#ffc107', 'color' => 'black']);
        DB::table('tables')->insert(['name' => 'ticket_prioridad', 'value' => 'critica', 'description' => 'Critica', 'background_color' => '#dc3545', 'color' => 'white']);
    }
}
