<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Coleccion;
use App\Models\User;
use App\Models\Xuxemon;

class ColeccionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $xuxemons = Xuxemon::all();

        // Para cada usuario, asignar algunos xuxemons aleatorios
        foreach ($users as $user) {
            // Determinar cuántos xuxemons tendrá este usuario (entre 1 y 5)
            $numXuxemons = rand(1, 5);

            // Obtener xuxemons aleatorios
            $xuxemonsAleatorios = $xuxemons->random(min($numXuxemons, $xuxemons->count()));

            foreach ($xuxemonsAleatorios as $xuxemon) {
                Coleccion::create([
                    'id_usuario' => $user->id,
                    'id_xuxemon' => $xuxemon->id,
                    'capturado_en' => now(),
                ]);
            }
        }
    }
}