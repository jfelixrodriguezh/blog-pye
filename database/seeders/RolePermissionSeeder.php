<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modulos = ['posts', 'autores', 'categorias', 'tags', 'podcasts', 'episodios', 'himnarios', 'tonos', 'himnos'];
        $acciones = ['ver', 'crear', 'editar', 'eliminar'];

        foreach ($modulos as $modulo) {
            foreach ($acciones as $accion) {
                Permission::firstOrCreate(['name' => "{$accion} {$modulo}"]);
            }
        }

        Permission::firstOrCreate(['name' => 'manage users']);
        Permission::firstOrCreate(['name' => 'manage posts']);
        Permission::firstOrCreate(['name' => 'manage podcasts']);
        Permission::firstOrCreate(['name' => 'manage himnos']);
        Permission::firstOrCreate(['name' => 'manage taxonomies']);
        Permission::firstOrCreate(['name' => 'manage videos']);

        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->syncPermissions(Permission::all());

        // Los demás roles (por módulo, por usuario, etc.) se definen manualmente
        // desde el panel de administración según se necesiten, no se crean por defecto aquí.

        $this->command->info('Permisos granulares y rol Admin actualizados.');
    }
}
