<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Xuxemon; // 👈 esto faltaba

class XuxemonSeeder extends Seeder
{
    public function run(): void
    {
        $xuxemons = [
            ['nombre' => 'Flamito',   'tipo' => 'fuego',     'evolucion' => 'base',  'vida' => 100, 'ataque' => 50, 'defensa' => 30, 'imagen' => null],
            ['nombre' => 'Flamardo',  'tipo' => 'fuego',     'evolucion' => 'media', 'vida' => 150, 'ataque' => 75, 'defensa' => 50, 'imagen' => null],
            ['nombre' => 'Flamaxus',  'tipo' => 'fuego',     'evolucion' => 'final', 'vida' => 200, 'ataque' => 110,'defensa' => 70, 'imagen' => null],
            ['nombre' => 'Aquito',    'tipo' => 'agua',      'evolucion' => 'base',  'vida' => 110, 'ataque' => 40, 'defensa' => 50, 'imagen' => null],
            ['nombre' => 'Aquardo',   'tipo' => 'agua',      'evolucion' => 'media', 'vida' => 160, 'ataque' => 65, 'defensa' => 75, 'imagen' => null],
            ['nombre' => 'Aquaxus',   'tipo' => 'agua',      'evolucion' => 'final', 'vida' => 210, 'ataque' => 90, 'defensa' => 110,'imagen' => null],
            ['nombre' => 'Territo',   'tipo' => 'tierra',    'evolucion' => 'base',  'vida' => 120, 'ataque' => 45, 'defensa' => 60, 'imagen' => null],
            ['nombre' => 'Electito',  'tipo' => 'electrico', 'evolucion' => 'base',  'vida' => 90,  'ataque' => 70, 'defensa' => 25, 'imagen' => null],
            ['nombre' => 'Normalix',  'tipo' => 'normal',    'evolucion' => 'base',  'vida' => 100, 'ataque' => 45, 'defensa' => 45, 'imagen' => null],
        ];

        foreach ($xuxemons as $x) {
            Xuxemon::create($x);
        }
    }
}
