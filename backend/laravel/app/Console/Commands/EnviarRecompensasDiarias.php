<?php

namespace App\Console\Commands;

use App\Models\Coleccion;
use App\Models\Item;
use App\Models\Mochila;
use App\Models\User;
use App\Models\Xuxemon;
use App\Models\ConfiguracionAdmin;
use Illuminate\Console\Command;

class EnviarRecompensasDiarias extends Command
{
    protected $signature   = 'xuxemons:recompensas-diarias';
    protected $description = 'Envía xuxes y un Xuxemon pequeño a todos los jugadores';

    public function handle(): void
    {
        $config    = ConfiguracionAdmin::obtener();
        $cantidad  = $config->cantidad_xuxes_diarias ?? 10;
        $xuxeItem  = Item::where('tipo', 'xuxe')->first();
        $jugadores = User::where('rol', 'jugador')->get();

        foreach ($jugadores as $jugador) {
            // Xuxes
            if ($xuxeItem) {
                $mochila = Mochila::where('id_usuario', $jugador->id)
                    ->where('id_item', $xuxeItem->id)
                    ->first();

                if ($mochila) {
                    $mochila->cantidad += $cantidad;
                    $mochila->save();
                } else {
                    $slot = (Mochila::where('id_usuario', $jugador->id)->max('slot') ?? 0) + 1;
                    Mochila::create([
                        'id_usuario' => $jugador->id,
                        'id_item'    => $xuxeItem->id,
                        'cantidad'   => $cantidad,
                        'slot'       => $slot,
                    ]);
                }
            }

            // Xuxemon pequeño
            $xuxemon = Xuxemon::where('tamaño', 'Pequeño')->inRandomOrder()->first();
            if ($xuxemon) {
                Coleccion::create([
                    'id_usuario'    => $jugador->id,
                    'id_xuxemon'    => $xuxemon->id,
                    'tamaño_actual' => 'Pequeño',
                ]);
            }

            $jugador->last_xuxes_diarios  = now();
            $jugador->last_xuxemon_diario = now();
            $jugador->save();

            $this->info("✅ Recompensas enviadas a {$jugador->name}");
        }

        $this->info('🎉 Recompensas diarias completadas.');
    }
}