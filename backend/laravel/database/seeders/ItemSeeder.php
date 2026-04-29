<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // Evita duplicats si ja existeixen
        if (Item::count() > 0) return;

        Item::insert([
            // Xuxes (apilables)
            ['nombre' => 'Xuxa Roja',  'tipo' => 'xuxe',   'icono' => '🍬', 'apilable' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Xuxa Blava', 'tipo' => 'xuxe',   'icono' => '💊', 'apilable' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Xuxa Verda', 'tipo' => 'xuxe',   'icono' => '🍏', 'apilable' => 1, 'created_at' => now(), 'updated_at' => now()],
            // Vacunes (no apilables)
            ['nombre' => 'Xocolatina',     'tipo' => 'vacuna', 'icono' => '🍫', 'apilable' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Xal de Fruites', 'tipo' => 'vacuna', 'icono' => '🍭', 'apilable' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Inxulina',       'tipo' => 'vacuna', 'icono' => '💉', 'apilable' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}