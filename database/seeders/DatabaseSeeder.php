<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BibliaSeeder::class,
            RolePermissionSeeder::class,
            AdminUserSeeder::class,
            HimnarioSeeder::class,

            // Datos de ejemplo/demo — solo para desarrollo local, no se
            // ejecutan en el servidor. Descomentar si se necesitan de nuevo.
            // AutorSeeder::class,
            // CategorySeeder::class,
            // TagSeeder::class,
            // PostSeeder::class,
        ]);
    }
}
