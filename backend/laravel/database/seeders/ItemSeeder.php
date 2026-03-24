<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        Item::insert([
            ['nombre' => 'Xuxe Roja',  'tipo' => 'xuxe',  'icono' => '🍬', 'apilable' => true,  'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Xuxe Blava', 'tipo' => 'xuxe',  'icono' => '🍭', 'apilable' => true,  'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Xuxe Verda', 'tipo' => 'xuxe',  'icono' => '🍡', 'apilable' => true,  'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Vacuna A',   'tipo' => 'vacuna', 'icono' => '💉', 'apilable' => false, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Vacuna B',   'tipo' => 'vacuna', 'icono' => '💊', 'apilable' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
