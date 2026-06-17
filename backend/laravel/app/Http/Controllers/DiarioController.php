<?php

namespace App\Http\Controllers;

use App\Models\Coleccion;
use App\Models\Item;
use App\Models\Mochila;
use App\Models\Xuxemon;
use App\Models\ConfiguracionAdmin;
use Illuminate\Http\Request;

class DiarioController extends Controller
{
    public function reclamarXuxesDiarios(Request $request)
    {
        $user = $request->user();

        if ($user->esAdmin()) {
            return response()->json(['message' => 'Los admins no reciben recompensas diarias'], 403);
        }

        if ($user->last_xuxes_diarios && $user->last_xuxes_diarios->isToday()) {
            return response()->json(['message' => 'Ya reclamaste tus xuxes hoy'], 400);
        }

        $config = ConfiguracionAdmin::obtener();
        $cantidad = $config->cantidad_xuxes_diarias ?? 10;

        $xuxeItem = Item::where('tipo', 'xuxe')->first();
        if (!$xuxeItem) {
            return response()->json(['message' => 'Item xuxe no encontrado'], 500);
        }

        $mochila = Mochila::where('id_usuario', $user->id)
            ->where('id_item', $xuxeItem->id)
            ->first();

        if ($mochila) {
            $mochila->cantidad += $cantidad;
            $mochila->save();
        } else {
            $slot = (Mochila::where('id_usuario', $user->id)->max('slot') ?? 0) + 1;
            Mochila::create([
                'id_usuario' => $user->id,
                'id_item'    => $xuxeItem->id,
                'cantidad'   => $cantidad,
                'slot'       => $slot,
            ]);
        }

        $user->last_xuxes_diarios = now();
        $user->save();

        return response()->json([
            'message'  => "Has recibido {$cantidad} xuxes",
            'cantidad' => $cantidad,
        ], 200);
    }

    public function reclamarXuxemonDiario(Request $request)
    {
        $user = $request->user();

        if ($user->esAdmin()) {
            return response()->json(['message' => 'Los admins no reciben recompensas diarias'], 403);
        }

        if ($user->last_xuxemon_diario && $user->last_xuxemon_diario->isToday()) {
            return response()->json(['message' => 'Ya reclamaste tu Xuxemon hoy'], 400);
        }

        $xuxemon = Xuxemon::where('tamaño', 'Pequeño')->inRandomOrder()->first();

        if (!$xuxemon) {
            return response()->json(['message' => 'No hay Xuxemons disponibles'], 500);
        }

        $coleccion = Coleccion::create([
            'id_usuario'    => $user->id,
            'id_xuxemon'    => $xuxemon->id,
            'tamaño_actual' => 'Pequeño',
        ]);

        $user->last_xuxemon_diario = now();
        $user->save();

        return response()->json([
            'message'   => 'Has recibido un nuevo Xuxemon',
            'xuxemon'   => $xuxemon,
            'coleccion' => $coleccion,
        ], 201);
    }
}