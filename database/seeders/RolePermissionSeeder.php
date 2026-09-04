<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            'manage posts',
            'manage podcasts',
            'manage himnos',
            'manage taxonomies',
            'manage users',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->syncPermissions(Permission::all());

        Role::firstOrCreate(['name' => 'Editor de Blog'])
            ->syncPermissions(['manage posts', 'manage taxonomies']);

        Role::firstOrCreate(['name' => 'Editor de Podcasts'])
            ->syncPermissions(['manage podcasts', 'manage taxonomies']);

        Role::firstOrCreate(['name' => 'Editor de Himnos'])
            ->syncPermissions(['manage himnos', 'manage taxonomies']);

        $this->command->info('Roles y permisos creados.');
    }
}
