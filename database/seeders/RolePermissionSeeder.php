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

        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->syncPermissions(Permission::all());

        Role::firstOrCreate(['name' => 'Post'])->syncPermissions([
            'ver posts', 'crear posts', 'editar posts', 'eliminar posts',
        ]);

        Role::firstOrCreate(['name' => 'Autor'])->syncPermissions([
            'ver autores', 'crear autores', 'editar autores', 'eliminar autores',
        ]);

        Role::firstOrCreate(['name' => 'Categoria'])->syncPermissions([
            'ver categorias', 'crear categorias', 'editar categorias', 'eliminar categorias',
        ]);

        Role::firstOrCreate(['name' => 'Tag'])->syncPermissions([
            'ver tags', 'crear tags', 'editar tags', 'eliminar tags',
        ]);

        Role::firstOrCreate(['name' => 'Podcasts'])->syncPermissions([
            'ver podcasts', 'crear podcasts', 'editar podcasts', 'eliminar podcasts',
        ]);

        Role::firstOrCreate(['name' => 'Episodios'])->syncPermissions([
            'ver episodios', 'crear episodios', 'editar episodios', 'eliminar episodios',
        ]);

        Role::firstOrCreate(['name' => 'Himnos'])->syncPermissions([
            'ver himnos', 'crear himnos', 'editar himnos', 'eliminar himnos',
        ]);

        Role::firstOrCreate(['name' => 'Himnarios'])->syncPermissions([
            'ver himnarios', 'crear himnarios', 'editar himnarios', 'eliminar himnarios',
        ]);

        Role::firstOrCreate(['name' => 'Tonos'])->syncPermissions([
            'ver tonos', 'crear tonos', 'editar tonos', 'eliminar tonos',
        ]);

        $this->command->info('Permisos granulares y roles actualizados.');
    }
}
