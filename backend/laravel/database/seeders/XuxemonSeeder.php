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
            'imagen' => 'Slime agua - 1.png',
        ]);

        Xuxemon::create([
            'nombre' => 'Onda Grande',
            'tipo' => 'Agua',
            'tamaño' => 'Mediano',
            'imagen' => 'Dragon agua - 2.png',
        ]);

        Xuxemon::create([
            'nombre' => 'Tsunami Rex',
            'tipo' => 'Agua',
            'tamaño' => 'Grande',
            'imagen' => 'Dragon agua - 3.png',
        ]);

        // Tierra
        Xuxemon::create([
            'nombre' => 'Pelito',
            'tipo' => 'Tierra',
            'tamaño' => 'Pequeño',
            'imagen' => 'Roca - 1.png',
        ]);

        Xuxemon::create([
            'nombre' => 'Peñarroja',
            'tipo' => 'Tierra',
            'tamaño' => 'Mediano',
            'imagen' => 'Golem roca - 2.png',
        ]);

        Xuxemon::create([
            'nombre' => 'Montaña Viva',
            'tipo' => 'Tierra',
            'tamaño' => 'Grande',
            'imagen' => 'Golem roca - 3.png',
        ]);

        // Aire
        Xuxemon::create([
            'nombre' => 'Ventita',
            'tipo' => 'Aire',
            'tamaño' => 'Pequeño',
            'imagen' => 'Cabra aire - 1.png',
        ]);

        Xuxemon::create([
            'nombre' => 'Torbellino',
            'tipo' => 'Aire',
            'tamaño' => 'Mediano',
            'imagen' => 'Cabra aire - 2.png',
        ]);

        Xuxemon::create([
            'nombre' => 'Huracán Mayor',
            'tipo' => 'Aire',
            'tamaño' => 'Grande',
            'imagen' => 'Cabra fuego - 3.png',
        ]);
    }
}

