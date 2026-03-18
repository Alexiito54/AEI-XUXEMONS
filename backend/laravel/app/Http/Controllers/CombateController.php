<?php

namespace App\Http\Controllers;

use App\Models\Combate;
use App\Models\Coleccion;
use Illuminate\Http\Request;

class CombateController extends Controller
{
    // GET /api/combates — historial de combates del entrenador autenticado
    public function index(Request $request)
    {
        $combates = Combate::with(['xuxemonRetador', 'xuxemonRival', 'retador', 'rival'])
            ->where('retador_id', $request->user()->user_id)
            ->orWhere('rival_id', $request->user()->user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($combates);
    }

    // GET /api/combates/{id} — ver detalle de un combate
    public function show($id)
    {
        $combate = Combate::with(['xuxemonRetador', 'xuxemonRival', 'retador', 'rival'])
            ->findOrFail($id);

        return response()->json($combate);
    }

    // POST /api/combates — iniciar un combate
    public function store(Request $request)
    {
        $request->validate([
            'rival_id'          => 'required|string|exists:users,user_id',
            'xuxemon_retador_id'=> 'required|exists:xuxemons,id',
            'xuxemon_rival_id'  => 'required|exists:xuxemons,id',
        ]);

        $retadorId = $request->user()->user_id;

        // No puedes retarte a ti mismo
        if ($retadorId === $request->rival_id) {
            return response()->json([
                'message' => 'No puedes retarte a ti mismo'
            ], 422);
        }

        // Verificar que el retador tiene ese Xuxemon en su colección
        $tieneXuxemon = Coleccion::where('user_id', $retadorId)
            ->where('xuxemon_id', $request->xuxemon_retador_id)
            ->exists();

        if (!$tieneXuxemon) {
            return response()->json([
                'message' => 'No tienes ese Xuxemon en tu colección'
            ], 422);
        }

        // Calcular ganador automáticamente por stats
        $ganador_id = $this->calcularGanador(
            $request->xuxemon_retador_id,
            $request->xuxemon_rival_id,
            $retadorId,
            $request->rival_id
        );

        $combate = Combate::create([
            'retador_id'          => $retadorId,
            'rival_id'            => $request->rival_id,
            'xuxemon_retador_id'  => $request->xuxemon_retador_id,
            'xuxemon_rival_id'    => $request->xuxemon_rival_id,
            'estado'              => 'finalizado',
            'ganador_id'          => $ganador_id,
        ]);

        return response()->json([
            'success'   => true,
            'combate'   => $combate->load(['xuxemonRetador', 'xuxemonRival']),
            'ganador_id'=> $ganador_id
        ], 201);
    }

    // Lógica de combate: gana el Xuxemon con mayor (ataque - defensa rival)
    private function calcularGanador($xuxemonRetadorId, $xuxemonRivalId, $retadorId, $rivalId): string
    {
        $xRetador = \App\Models\Xuxemon::find($xuxemonRetadorId);
        $xRival   = \App\Models\Xuxemon::find($xuxemonRivalId);

        $danoRetador = max(0, $xRetador->ataque - $xRival->defensa);
        $danoRival   = max(0, $xRival->ataque   - $xRetador->defensa);

        if ($danoRetador > $danoRival) return $retadorId;
        if ($danoRival > $danoRetador) return $rivalId;

        // Empate: gana quien tenga más vida
        return $xRetador->vida >= $xRival->vida ? $retadorId : $rivalId;
    }
}
