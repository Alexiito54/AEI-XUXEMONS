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
            'nombre' => 'Droplet',
            'tipo' => 'Agua',
            'tamaño' => 'Pequeño',
            'imagen' => 'Slime agua - 1.png',
        ]);

        Xuxemon::create([
            'nombre' => 'Aquarion',
            'tipo' => 'Agua',
            'tamaño' => 'Mediano',
            'imagen' => 'Slime agua - 2.png',
        ]);

        Xuxemon::create([
            'nombre' => 'Hydronix',
            'tipo' => 'Agua',
            'tamaño' => 'Grande',
            'imagen' => 'Slime agua - 3.png',
        ]);

        Xuxemon::create([
            'nombre' => 'Whispu',
            'tipo' => 'Agua',
            'tamaño' => 'Pequeño',
            'imagen' => 'Dragon agua - 1.png',
        ]);

        Xuxemon::create([
            'nombre' => 'Galeon',
            'tipo' => 'Agua',
            'tamaño' => 'Mediano',
            'imagen' => 'Dragon agua - 2.png',
        ]);

        Xuxemon::create([
            'nombre' => 'Aequor',
            'tipo' => 'Agua',
            'tamaño' => 'Grande',
            'imagen' => 'Dragon agua - 3.png',
        ]);

/*  -----  Tierra   -----  */
        Xuxemon::create([
            'nombre' => 'Titagranito',
            'tipo' => 'Tierra',
            'tamaño' => 'Pequeño',
            'imagen' => 'Golem Roca - 1.png',
        ]);

        Xuxemon::create([
            'nombre' => 'Monolito',
            'tipo' => 'Tierra',
            'tamaño' => 'Mediano',
            'imagen' => 'Golem roca - 2.png',
        ]);

        Xuxemon::create([
            'nombre' => 'Terratitan',
            'tipo' => 'Tierra',
            'tamaño' => 'Grande',
            'imagen' => 'Golem roca - 3.png',
        ]);

        Xuxemon::create([
            'nombre' => 'Granadillo',
            'tipo' => 'Tierra',
            'tamaño' => 'Pequeño',
            'imagen' => 'Roca - 1.png',
        ]);

        Xuxemon::create([
            'nombre' => 'Titanadillo',
            'tipo' => 'Tierra',
            'tamaño' => 'Mediano',
            'imagen' => 'Roca - 2.png',
        ]);

        Xuxemon::create([
            'nombre' => 'Tornadillo',
            'tipo' => 'Tierra',
            'tamaño' => 'Grande',
            'imagen' => 'Roca - 3.png',
        ]);

/* ----- Aire ----- */
        Xuxemon::create([
            'nombre' => 'Sheepwind',
            'tipo' => 'Aire',
            'tamaño' => 'Pequeño',
            'imagen' => 'Cabra aire - 1.png',
        ]);

        Xuxemon::create([
            'nombre' => 'Aerofluff',
            'tipo' => 'Aire',
            'tamaño' => 'Mediano',
            'imagen' => 'Cabra aire - 2.png',
        ]);

        Xuxemon::create([
            'nombre' => 'Celestuff',
            'tipo' => 'Aire',
            'tamaño' => 'Grande',
            'imagen' => 'Cabra aire - 3.png',
        ]);
    }
}

