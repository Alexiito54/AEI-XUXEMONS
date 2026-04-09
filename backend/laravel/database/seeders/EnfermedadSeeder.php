<?php

namespace Database\Seeders;

use App\Models\Enfermedad;
use Illuminate\Database\Seeder;

class EnfermedadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $enfermedades = [
            [
                'nombre' => 'Bajón de azúcar',
                'descripcion' => 'Requiere +2 xuxes por nivel para crecer',
                'xuxes_extra_para_crecer' => 2,
                'impide_alimentacion' => false,
                'porcentaje_infeccion' => 5,
            ],
            [
                'nombre' => 'Sobredosis de sucre',
                'descripcion' => 'El Xuxemon sufre de exceso de azúcar',
                'xuxes_extra_para_crecer' => 0,
                'impide_alimentacion' => false,
                'porcentaje_infeccion' => 10,
            ],
            [
                'nombre' => 'Atracón',
                'descripcion' => 'El Xuxemon no puede alimentarse',
                'xuxes_extra_para_crecer' => 0,
                'impide_alimentacion' => true,
                'porcentaje_infeccion' => 15,
            ],
        ];

        foreach ($enfermedades as $enfermedad) {
            Enfermedad::create($enfermedad);
        }
    }
}
