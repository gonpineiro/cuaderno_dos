<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sudoRole = Role::create(['name' => 'sudo', 'description' => 'SUDO']);
        $sudo = User::where('email', 'sudo@allenderepuestos.com.ar')->first();
        $sudo->assignRole($sudoRole);

        $adminRole = Role::create(['name' => 'admin', 'description' => 'Administrador']);
        $allende = User::where('email', 'allende@allenderepuestos.com.ar')->first();
        $allende->assignRole($adminRole);

        $vendedor_mostrador = Role::create(['name' => 'vendedor_mostrador', 'description' => 'Vendedor mostrador']);
        $vendedor_whatsapp = Role::create(['name' => 'vendedor_whatsapp', 'description' => 'Vendedor whatsapp']);
        $siniestro = Role::create(['name' => 'siniestro', 'description' => 'Siniestro']);
        $compras = Role::create(['name' => 'compras', 'description' => 'Compras']);
        $deposito = Role::create(['name' => 'deposito', 'description' => 'Deposito']);
        $caja = Role::create(['name' => 'caja', 'description' => 'Caja']);

        // Crear usuarios y asignar roles
        $users = [
            'vendedor_mostrador' => [
                'Oscar Barigelli', 'Maximiliano Perello', 'Jorge Riquelme',
                'Santiago Fuentes', 'Tomas Schawb', 'Sebastian Fernandez', 'Ivan Escobar'
            ],
            'vendedor_whatsapp' => [
                'Sebastian De Haro', 'Leandro Almazabal', 'Joaquin Berrios', 'Sebastian Fernandez'
            ],
            'compras' => ['Ivan Escobar'],
            'deposito' => ['Luciano Lucero', 'Brian'],
            'siniestro' => ['Ignacio Paglieri', 'Sebastian Fernandez']
        ];

        foreach ($users as $role => $names) {
            foreach ($names as $name) {
                $email = strtolower(str_replace(' ', '', $name)) . '@allenderespuestos.com.ar';
                $password = Hash::make(strtolower(str_replace(' ', '', $name)));

                $user = User::where('email', $email)->first();

                if (!$user) {
                    $user = User::create([
                        'name' => $name,
                        'email' => $email,
                        'password' => $password,
                    ]);
                }

                switch ($role) {
                    case 'vendedor_mostrador':
                        $user->assignRole($vendedor_mostrador);
                        break;
                    case 'vendedor_whatsapp':
                        $user->assignRole($vendedor_whatsapp);
                        break;
                    case 'compras':
                        $user->assignRole($compras);
                        break;
                    case 'deposito':
                        $user->assignRole($deposito);
                        break;
                    case 'siniestro':
                        $user->assignRole($siniestro);
                        break;
                }
            }
        }
    }
}
