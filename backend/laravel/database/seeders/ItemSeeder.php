<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Item::create([
            'nombre' => 'Xuxe Roja',
            'tipo' => 'xuxe',
            'icono' => '🍬',
            'apilable' => true,
        ]);

        Item::create([
            'nombre' => 'Xuxe Blava',
            'tipo' => 'xuxe',
            'icono' => '🍭',
            'apilable' => true,
        ]);

        Item::create([
            'nombre' => 'Xuxe Verda',
            'tipo' => 'xuxe',
            'icono' => '🍡',
            'apilable' => true,
        ]);

        // Vacunas específicas para NIVEL 3
        Item::create([
            'nombre' => 'Xocolatina',
            'tipo' => 'vacuna',
            'icono' => '🍫',
            'apilable' => false,
        ]);

        Item::create([
            'nombre' => 'Xal de fruites',
            'tipo' => 'vacuna',
            'icono' => '🍉',
            'apilable' => false,
        ]);

        Item::create([
            'nombre' => 'Inxulina',
            'tipo' => 'vacuna',
            'icono' => '💊',
            'apilable' => false,
        ]);
    }
}

