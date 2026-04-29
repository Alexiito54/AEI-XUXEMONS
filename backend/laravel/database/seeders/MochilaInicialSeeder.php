<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Mochila;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MochilaInicialSeeder extends Seeder
{
    public function run(): void
    {
        $xuxes   = Item::where('tipo', 'xuxe')->get();
        $vacunes = Item::where('tipo', 'vacuna')->get();

        if ($xuxes->isEmpty()) {
            $this->command->warn('No hay items tipo xuxe.');
            return;
        }

        // ✅ Usa id_usuario en lloc de user_id
        $usuarisAmbMochila = DB::table('mochila')
            ->pluck('id_usuario')
            ->unique()
            ->toArray();

        $usuarisSenseItems = User::whereNotIn('id', $usuarisAmbMochila)->get();

        foreach ($usuarisSenseItems as $user) {
            $itemsAInserir = [];
            $slot = 1;

            foreach ($xuxes as $xuxa) {
                if ($slot > 20) break;
                $itemsAInserir[] = [
                    'id_usuario' => $user->id,
                    'id_item'    => $xuxa->id,
                    'cantidad'   => rand(1, 5),
                    'slot'       => $slot++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if ($slot <= 20 && $vacunes->isNotEmpty()) {
                $vacuna = $vacunes->random();
                $itemsAInserir[] = [
                    'id_usuario' => $user->id,
                    'id_item'    => $vacuna->id,
                    'cantidad'   => 1,
                    'slot'       => $slot,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            Mochila::insert($itemsAInserir);
            $this->command->info("✅ Items donats a: {$user->name}");
        }

        $this->command->info("🎒 Completat per {$usuarisSenseItems->count()} usuaris.");
    }
}