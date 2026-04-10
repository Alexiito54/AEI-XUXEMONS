<?php

namespace App\Http\Controllers;

use App\Models\Mochila;
use App\Models\Item;
use Illuminate\Http\Request;

class MochilaController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $mochilaItems = Mochila::where('id_usuario', $user->id)
            ->with('articulo')
            ->get();

        return response()->json($mochilaItems, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_item' => 'required|exists:items,id',
            'cantidad' => 'required|integer|min:1',
            'id_usuario' => 'required|exists:users,id',
        ]);

        $user = $request->user();
        $targetUserId = $validated['id_usuario'];

        if ($user->id != $targetUserId && $user->rol !== 'administrador') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $item = Item::find($validated['id_item']);
        $cantidad = $validated['cantidad'];

        // Contar slots ocupados en la mochila del jugador objetivo
        $slotsOcupados = Mochila::where('id_usuario', $targetUserId)->count();
        if ($slotsOcupados >= 20) {
            return response()->json(['message' => 'Mochila llena'], 400);
        }

        if ($item->apilable) {
            // Xuxes: máx 5 por slot
            $existing = Mochila::where('id_usuario', $targetUserId)
                ->where('id_item', $item->id)
                ->first();

            if ($existing && $existing->cantidad < 5) {
                $canAdd = min(5 - $existing->cantidad, $cantidad);
                $existing->cantidad += $canAdd;
                $existing->save();
                $cantidad -= $canAdd;
            }

            while ($cantidad > 0 && $slotsOcupados < 20) {
                $cantidadSlot = min(5, $cantidad);
                Mochila::create([
                    'id_usuario' => $targetUserId,
                    'id_item' => $item->id,
                    'cantidad' => $cantidadSlot,
                ]);
                $cantidad -= $cantidadSlot;
                $slotsOcupados++;
            }
        } else {
            // Vacunas: 1 por slot
            for ($i = 0; $i < $cantidad && $slotsOcupados < 20; $i++) {
                Mochila::create([
                    'id_usuario' => $targetUserId,
                    'id_item' => $item->id,
                    'cantidad' => 1,
                ]);
                $cantidad--;
                $slotsOcupados++;
            }
        }

        $message = 'Artículo añadido';
        if ($cantidad > 0) {
            $message = 'Artículo añadido. Algunas unidades sobrantes fueron descartadas porque la mochila estaba llena.';
        }

        return response()->json(['message' => $message], 201);
    }

    public function destroy(Request $request, Mochila $mochila)
    {
        if ($mochila->id_usuario != $request->user()->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $mochila->delete();
        return response()->json(['message' => 'Artículo eliminado'], 200);
    }
}

