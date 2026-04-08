<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si el usuario admin ya existe
        if (!User::where('email', 'admin@xuxemons.com')->exists()) {
            User::create([
                'name' => 'Admin',
                'apellidos' => 'Xuxemons',
                'email' => 'admin@xuxemons.com',
                'password' => Hash::make('admin123456'),
                'id_usuario' => '#AdminXUXEMONS',
                'rol' => 'administrador',
            ]);

            echo "\n✅ Usuario administrador creado exitosamente!\n";
            echo "📋 Credenciales de acceso:\n";
            echo "   ID de Usuario: #AdminXUXEMONS\n";
            echo "   Contraseña: admin123456\n";
            echo "   Email: admin@xuxemons.com\n\n";
        } else {
            echo "\n⚠️ El usuario administrador ya existe!\n\n";
        }
    }
}

