<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Xuxemon;

class XuxemonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Agua
        Xuxemon::create([
            'nombre' => 'Aguachu',
            'tipo' => 'Agua',
            'tamaño' => 'Pequeño',
        ]);

        Xuxemon::create([
            'nombre' => 'Onda Grande',
            'tipo' => 'Agua',
            'tamaño' => 'Mediano',
        ]);

        Xuxemon::create([
            'nombre' => 'Tsunami Rex',
            'tipo' => 'Agua',
            'tamaño' => 'Grande',
        ]);

        // Tierra
        Xuxemon::create([
            'nombre' => 'Pelito',
            'tipo' => 'Tierra',
            'tamaño' => 'Pequeño',
        ]);

        Xuxemon::create([
            'nombre' => 'Peñarroja',
            'tipo' => 'Tierra',
            'tamaño' => 'Mediano',
        ]);

        Xuxemon::create([
            'nombre' => 'Montaña Viva',
            'tipo' => 'Tierra',
            'tamaño' => 'Grande',
        ]);

        // Aire
        Xuxemon::create([
            'nombre' => 'Ventita',
            'tipo' => 'Aire',
            'tamaño' => 'Pequeño',
        ]);

        Xuxemon::create([
            'nombre' => 'Torbellino',
            'tipo' => 'Aire',
            'tamaño' => 'Mediano',
        ]);

        Xuxemon::create([
            'nombre' => 'Huracán Mayor',
            'tipo' => 'Aire',
            'tamaño' => 'Grande',
        ]);
    }
}

