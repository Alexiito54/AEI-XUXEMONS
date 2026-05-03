<?php

namespace Database\Seeders;

use App\Models\Xuxemon;
use Illuminate\Database\Seeder;

class EvolucionSeeder extends Seeder
{
    public function run(): void
    {
        $evoluciones = [
            // Agua línea 1
            'Droplet'    => 'Aquarion',
            'Aquarion'   => 'Hydronix',
            // Agua línea 2
            'Whispu'     => 'Galeon',
            'Galeon'     => 'Aequor',
            // Tierra línea 1
            'Titagranito' => 'Monolito',
            'Monolito'    => 'Terratitan',
            // Tierra línea 2
            'Granadillo'  => 'Titanadillo',
            'Titanadillo' => 'Tornadillo',
            // Aire línea 1
            'Sheepwind'  => 'Aerofluff',
            'Aerofluff'  => 'Celestuff',
            // Aire línea 2
            'Dragofluff' => 'Kingfluff',
            'Kingfluff'  => 'Ancientus',
        ];

        foreach ($evoluciones as $nombreOrigen => $nombreDestino) {
            $origen  = Xuxemon::where('nombre', $nombreOrigen)->first();
            $destino = Xuxemon::where('nombre', $nombreDestino)->first();

            if ($origen && $destino) {
                $origen->evoluciona_a = $destino->id;
                $origen->save();
                $this->command->info("✅ {$nombreOrigen} → {$nombreDestino}");
            }
        }

        $this->command->info('🎉 Evoluciones configuradas.');
    }
}