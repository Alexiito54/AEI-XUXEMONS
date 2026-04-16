<?php

namespace Database\Seeders;

use App\Models\Vacuna;
use App\Models\Enfermedad;
use Illuminate\Database\Seeder;

class VacunaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bajon = Enfermedad::where('nombre', 'Bajón de azúcar')->first();
        $atracón = Enfermedad::where('nombre', 'Atracón')->first();

        $vacunas = [
            [
                'nombre' => 'Xocolatina',
                'cura_enfermedad_id' => $bajon->id ?? null,
            ],
            [
                'nombre' => 'Xal de fruites',
                'cura_enfermedad_id' => $atracón->id ?? null,
            ],
            [
                'nombre' => 'Inxulina',
                'cura_enfermedad_id' => null, 
            ],
        ];

        foreach ($vacunas as $vacuna) {
            Vacuna::create($vacuna);
        }
    }
}
