<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuarios = [
            ['name' => 'Juan', 'apellidos' => 'García', 'email' => 'juan@test.com', 'id_usuario' => 'juan123'],
            ['name' => 'María', 'apellidos' => 'López', 'email' => 'maria@test.com', 'id_usuario' => 'maria123'],
            ['name' => 'Carlos', 'apellidos' => 'Rodríguez', 'email' => 'carlos@test.com', 'id_usuario' => 'carlos123'],
            ['name' => 'Ana', 'apellidos' => 'Martínez', 'email' => 'ana@test.com', 'id_usuario' => 'ana123'],
            ['name' => 'Pedro', 'apellidos' => 'Pérez', 'email' => 'pedro@test.com', 'id_usuario' => 'pedro123'],
            ['name' => 'Laura', 'apellidos' => 'Gómez', 'email' => 'laura@test.com', 'id_usuario' => 'laura123'],
            ['name' => 'Diego', 'apellidos' => 'Fernández', 'email' => 'diego@test.com', 'id_usuario' => 'diego123'],
            ['name' => 'Sofia', 'apellidos' => 'Sánchez', 'email' => 'sofia@test.com', 'id_usuario' => 'sofia123'],
            ['name' => 'Roberto', 'apellidos' => 'Torres', 'email' => 'roberto@test.com', 'id_usuario' => 'roberto123'],
            ['name' => 'Elena', 'apellidos' => 'Jiménez', 'email' => 'elena@test.com', 'id_usuario' => 'elena123'],
        ];

        foreach ($usuarios as $usuario) {
            if (!User::where('email', $usuario['email'])->exists()) {
                User::create([
                    'name' => $usuario['name'],
                    'apellidos' => $usuario['apellidos'],
                    'email' => $usuario['email'],
                    'password' => Hash::make('password123'),
                    'id_usuario' => $usuario['id_usuario'],
                    'rol' => 'user',
                ]);

                echo "Usuario '{$usuario['name']}' creado correctamente\n";
            }
        }
    }
}
