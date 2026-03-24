<?php

namespace App\Http\Controllers;

use App\Models\Mochila;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MochilaController extends Controller
{
    public function index()
    {
        $mochila = Mochila::with('item')
            ->where('user_id', Auth::id())
            ->orderBy('slot')
            ->get();

        return response()->json($mochila);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'  => 'required|exists:users,id',
            'item_id'  => 'required|exists:items,id',
            'cantidad' => 'required|integer|min:1|max:5',
        ]);

        $ocupados = Mochila::where('user_id', $request->user_id)->count();
        if ($ocupados >= 20) {
            return response()->json(['message' => 'Mochila llena'], 400);
        }

        // Buscar primer slot libre
        $slotsOcupados = Mochila::where('user_id', $request->user_id)
            ->pluck('slot')->toArray();
        $slotLibre = 0;
        while (in_array($slotLibre, $slotsOcupados)) $slotLibre++;

        $entrada = Mochila::create([
            'user_id'  => $request->user_id,
            'item_id'  => $request->item_id,
            'cantidad' => $request->cantidad,
            'slot'     => $slotLibre,
        ]);

        return response()->json($entrada->load('item'), 201);
    }

    public function destroy($id)
    {
        $entrada = Mochila::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $entrada->delete();

        return response()->json(['message' => 'Item eliminado']);
    }
}
