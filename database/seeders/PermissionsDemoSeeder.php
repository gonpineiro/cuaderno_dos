<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsDemoSeeder extends Seeder
{
    /**
     * Create the initial roles and permissions.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'permission.view', 'description' => 'Puede ver permisos']);
        Permission::create(['name' => 'permission.asign', 'description' => 'Puede otorgar o quitar permisos']);

        // create roles and assign existing permissions
        $role1 = Role::create(['name' => 'sudo', 'description' => 'SUDO']);
        $role1->givePermissionTo('permission.view');
        $role1->givePermissionTo('permission.asign');

        /* Cotizaciones */
        Permission::create(['name' => 'cotizacion.view', 'description' => 'Puede ver cotizaciones']);
        Permission::create(['name' => 'cotizacion.create', 'description' => 'Puede crear cotizaciones']);
        Permission::create(['name' => 'cotizacion.edit', 'description' => 'Puede editar cotizaciones']);
        Permission::create(['name' => 'cotizacion.delete', 'description' => 'Puede borrar cotizaciones']);

        $role_cotizaciones  = Role::create(['name' => 'cotizacion', 'description' => 'Administrador de cotizaciones']);
        $role_cotizaciones->givePermissionTo('cotizacion.view');
        $role_cotizaciones->givePermissionTo('cotizacion.create');
        $role_cotizaciones->givePermissionTo('cotizacion.edit');
        $role_cotizaciones->givePermissionTo('cotizacion.delete');

        /* Pedidos */
        Permission::create(['name' => 'pedido.view', 'description' => 'Puede ver pedidos']);
        Permission::create(['name' => 'pedido.create', 'description' => 'Puede crear pedidos']);
        Permission::create(['name' => 'pedido.edit', 'description' => 'Puede editar pedidos']);
        Permission::create(['name' => 'pedido.delete', 'description' => 'Puede borrar pedidos']);

        $role_pedidos  = Role::create(['name' => 'pedido', 'description' => 'Administrador de pedidos']);
        $role_pedidos->givePermissionTo('pedido.view');
        $role_pedidos->givePermissionTo('pedido.create');
        $role_pedidos->givePermissionTo('pedido.edit');
        $role_pedidos->givePermissionTo('pedido.delete');
    }
}
