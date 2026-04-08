<?php

namespace App\Http\Controllers;

use App\Models\Coleccion;
use App\Models\Xuxemon;
use Illuminate\Http\Request;

class ColeccionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $coleccion = Coleccion::where('id_usuario', $user->id)
            ->with('xuxemon')
            ->get();

        return response()->json($coleccion, 200);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        // Obtener un Xuxemon aleatorio
        $xuxemon = Xuxemon::inRandomOrder()->first();

        if (!$xuxemon) {
            return response()->json(['message' => 'No hay xuxemons disponibles'], 404);
        }

        $coleccion = Coleccion::create([
            'id_usuario' => $user->id,
            'id_xuxemon' => $xuxemon->id,
        ]);

        return response()->json([
            'message' => 'Xuxemon capturado',
            'xuxemon' => $xuxemon,
            'coleccion' => $coleccion,
        ], 201);
    }

    public function destroy(Request $request, Coleccion $coleccion)
    {
        if ($coleccion->id_usuario != $request->user()->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $coleccion->delete();
        return response()->json(['message' => 'Xuxemon eliminado'], 200);
    }
}

