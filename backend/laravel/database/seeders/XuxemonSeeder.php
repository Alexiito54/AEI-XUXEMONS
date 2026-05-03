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
            'imagen' => 'Slime agua - 1.webp',
        ]);

        Xuxemon::create([
            'nombre' => 'Aquarion',
            'tipo' => 'Agua',
            'tamaño' => 'Mediano',
            'imagen' => 'Slime agua - 2.webp',
        ]);

        Xuxemon::create([
            'nombre' => 'Hydronix',
            'tipo' => 'Agua',
            'tamaño' => 'Grande',
            'imagen' => 'Slime agua - 3.webp',
        ]);

        Xuxemon::create([
            'nombre' => 'Whispu',
            'tipo' => 'Agua',
            'tamaño' => 'Pequeño',
            'imagen' => 'Dragon agua - 1.webp',
        ]);

        Xuxemon::create([
            'nombre' => 'Galeon',
            'tipo' => 'Agua',
            'tamaño' => 'Mediano',
            'imagen' => 'Dragon agua - 2.webp',
        ]);

        Xuxemon::create([
            'nombre' => 'Aequor',
            'tipo' => 'Agua',
            'tamaño' => 'Grande',
            'imagen' => 'Dragon agua - 3.webp',
        ]);

/*  -----  Tierra   -----  */
        Xuxemon::create([
            'nombre' => 'Titagranito',
            'tipo' => 'Tierra',
            'tamaño' => 'Pequeño',
            'imagen' => 'Golem roca - 1.webp',
        ]);

        Xuxemon::create([
            'nombre' => 'Monolito',
            'tipo' => 'Tierra',
            'tamaño' => 'Mediano',
            'imagen' => 'Golem roca - 2.webp',
        ]);

        Xuxemon::create([
            'nombre' => 'Terratitan',
            'tipo' => 'Tierra',
            'tamaño' => 'Grande',
            'imagen' => 'Golem roca - 3.webp',
        ]);

        Xuxemon::create([
            'nombre' => 'Granadillo',
            'tipo' => 'Tierra',
            'tamaño' => 'Pequeño',
            'imagen' => 'Roca - 1.webp',
        ]);

        Xuxemon::create([
            'nombre' => 'Titanadillo',
            'tipo' => 'Tierra',
            'tamaño' => 'Mediano',
            'imagen' => 'Roca - 2.webp',
        ]);

        Xuxemon::create([
            'nombre' => 'Tornadillo',
            'tipo' => 'Tierra',
            'tamaño' => 'Grande',
            'imagen' => 'Roca - 3.webp',
        ]);

/* ----- Aire ----- */
        Xuxemon::create([
            'nombre' => 'Sheepwind',
            'tipo' => 'Aire',
            'tamaño' => 'Pequeño',
            'imagen' => 'Cabra aire - 1.webp',
        ]);

        Xuxemon::create([
            'nombre' => 'Aerofluff',
            'tipo' => 'Aire',
            'tamaño' => 'Mediano',
            'imagen' => 'Cabra aire - 2.webp',
        ]);

        Xuxemon::create([
            'nombre' => 'Celestuff',
            'tipo' => 'Aire',
            'tamaño' => 'Grande',
            'imagen' => 'Cabra aire - 3.webp',
        ]);

                Xuxemon::create([
            'nombre' => 'Dragofluff',
            'tipo' => 'Aire',
            'tamaño' => 'Pequeño',
            'imagen' => 'Dragon aire - 1.webp',
        ]);

        Xuxemon::create([
            'nombre' => 'Kingfluff',
            'tipo' => 'Aire',
            'tamaño' => 'Mediano',
            'imagen' => 'Dragon aire - 2.webp',
        ]);

        Xuxemon::create([
            'nombre' => 'Ancientus',
            'tipo' => 'Aire',
            'tamaño' => 'Grande',
            'imagen' => 'Dragon aire - 3.webp',
        ]);
    }
}

