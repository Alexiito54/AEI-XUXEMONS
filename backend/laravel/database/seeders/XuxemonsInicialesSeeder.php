<?php

namespace Database\Seeders;

use App\Models\Coleccion;
use App\Models\User;
use App\Models\Xuxemon;
use Illuminate\Database\Seeder;

class XuxemonsInicialesSeeder extends Seeder
{
    public function run(): void
    {
        $jugadores = User::where('rol', 'jugador')->get();
        $xuxemonsPequenos = Xuxemon::where('tamaño', 'Pequeño')->get();

        if ($xuxemonsPequenos->isEmpty()) {
            $this->command->warn('⚠️  No hay Xuxemons de tamaño Pequeño.');
            return;
        }

        foreach ($jugadores as $jugador) {
            $tieneColeccion = Coleccion::where('id_usuario', $jugador->id)->exists();
            if ($tieneColeccion) {
                $this->command->info("⏭️  {$jugador->name} ya tiene Xuxemons, se omite.");
                continue;
            }

            $cantidad = min(3, $xuxemonsPequenos->count());
            $seleccionados = $xuxemonsPequenos->random($cantidad);

            foreach ($seleccionados as $xuxemon) {
                Coleccion::create([
                    'id_usuario'   => $jugador->id,
                    'id_xuxemon'   => $xuxemon->id,
                    'tamaño_actual' => 'Pequeño',
                ]);
            }

            $this->command->info("✅ {$jugador->name} recibe {$cantidad} Xuxemons iniciales.");
        }

        $this->command->info('🎉 XuxemonsInicialesSeeder completado.');
    }
}