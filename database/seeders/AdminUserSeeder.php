<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Crea (o actualiza) el usuario administrador por defecto del blog.
     *
     * Nota: la tabla users no tiene columna "usuario"/username, el login
     * se hace por email. El "pyeadmin" pedido se usa como name del usuario.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'pyeadmin@correo.com'],
            [
                'name' => 'pyeadmin',
                'password' => Hash::make('*159753*PyE'),
                'email_verified_at' => now(),
            ]
        );

        // Requiere que RolePermissionSeeder ya haya creado el rol "Admin".
        $admin->syncRoles(['Admin']);

        $this->command->info('Usuario admin por defecto listo: pyeadmin@correo.com');
    }
}
