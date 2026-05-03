<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Coleccion;
use App\Models\Xuxemon;
use App\Models\Mochila;
use App\Models\Item;
use App\Models\Enfermedad;
use App\Models\Vacuna;
use App\Models\ConfiguracionAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ColeccionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $coleccion = Coleccion::where('id_usuario', $user->id)
            ->with(['xuxemon', 'enfermedades'])
            ->get();

        return response()->json($coleccion, 200);
    }

    public function xuxedex(Request $request)
    {
        $user = $request->user();
        $isAdmin = $user->esAdmin();

        $todosXuxemons = Xuxemon::all();

        $capturasPorXuxemon = Coleccion::where('id_usuario', $user->id)
            ->select('id_xuxemon', DB::raw('COUNT(*) as total'))
            ->groupBy('id_xuxemon')
            ->pluck('total', 'id_xuxemon');
        $xuxemonsCapturados = $capturasPorXuxemon->keys()->map(fn ($id) => (int) $id)->all();

        $resultado = [];

        if ($isAdmin) {
            foreach ($todosXuxemons as $xuxemon) {
                $instancias = Coleccion::where('id_usuario', $user->id)
                    ->where('id_xuxemon', $xuxemon->id)
                    ->with('enfermedades')
                    ->get()
                    ->map(fn($c) => [
                        'coleccion_id'              => $c->id,
                        'tamano_actual'             => $c->tamaño_actual,
                        'nivel'                     => $c->nivel,
                        'alimentaciones_pendientes' => $c->alimentaciones_pendientes,
                        'esta_enfermo'              => $c->estaEnfermo(),
                        'enfermedades'              => $c->enfermedades->map(fn($e) => [
                            'id'     => $e->id,
                            'nombre' => $e->nombre,
                        ]),
                    ]);

                $resultado[] = [
                    'id'                 => $xuxemon->id,
                    'nombre'             => $xuxemon->nombre,
                    'tipo'               => $xuxemon->tipo,
                    'tamaño'             => $xuxemon->tamaño,
                    'imagen'             => $xuxemon->imagen,
                    'cantidad_capturada' => (int) ($capturasPorXuxemon->get($xuxemon->id, 0)),
                    'atrapado'           => true,
                    'visto'              => true,
                    'oculto'             => false,
                    'instancias'         => $instancias,
                ];
            }
        } else {
            $totalXuxemons = $todosXuxemons->count();
            $mitad = (int) ceil($totalXuxemons / 2);

            $xuxemonsDisponibles = $todosXuxemons->pluck('id')->toArray();
            mt_srand($user->id);
            shuffle($xuxemonsDisponibles);
            $xuxemonsVisibles = array_slice($xuxemonsDisponibles, 0, $mitad);

            foreach ($todosXuxemons as $xuxemon) {
                $estaAtrapado = in_array($xuxemon->id, $xuxemonsCapturados);
                $estaVisible  = in_array($xuxemon->id, $xuxemonsVisibles);
                $esVisto      = $estaVisible || $estaAtrapado;
                $esOculto     = !$esVisto;

                $instancias = Coleccion::where('id_usuario', $user->id)
                    ->where('id_xuxemon', $xuxemon->id)
                    ->with('enfermedades')
                    ->get()
                    ->map(fn($c) => [
                        'coleccion_id'              => $c->id,
                        'tamano_actual'             => $c->tamaño_actual,
                        'nivel'                     => $c->nivel,
                        'alimentaciones_pendientes' => $c->alimentaciones_pendientes,
                        'esta_enfermo'              => $c->estaEnfermo(),
                        'enfermedades'              => $c->enfermedades->map(fn($e) => [
                            'id'     => $e->id,
                            'nombre' => $e->nombre,
                        ]),
                    ]);

                $resultado[] = [
                    'id'                 => $xuxemon->id,
                    'nombre'             => $xuxemon->nombre,
                    'tipo'               => $xuxemon->tipo,
                    'tamaño'             => $xuxemon->tamaño,
                    'imagen'             => $xuxemon->imagen,
                    'cantidad_capturada' => (int) ($capturasPorXuxemon->get($xuxemon->id, 0)),
                    'atrapado'           => $estaAtrapado,
                    'visto'              => $esVisto,
                    'oculto'             => $esOculto,
                    'instancias'         => $instancias,
                ];
            }
        }

        return response()->json([
            'xuxemons' => $resultado,
            'estadisticas' => [
                'total'    => $todosXuxemons->count(),
                'atrapados' => $isAdmin ? $todosXuxemons->count() : count(array_filter($resultado, fn($x) => $x['atrapado'])),
                'vistos'   => $isAdmin ? $todosXuxemons->count() : count(array_filter($resultado, fn($x) => $x['visto'])),
                'is_admin' => $isAdmin,
            ]
        ], 200);
    }
    public function indexForUser(User $user)
{
    $coleccion = Coleccion::with('xuxemon')
        ->where('id_usuario', $user->id)
        ->get();

    return response()->json([
        'jugador' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ],
        'coleccion' => $coleccion,
        'total' => $coleccion->count(),
    ], 200);
}

    public function store(Request $request)
    {
        $user = $request->user();
        $resultado = $this->crearXuxemonAleatorioParaUsuario($user);

        if (!$resultado) {
            return response()->json(['message' => 'No hay xuxemons disponibles'], 404);
        }

        [$xuxemon, $coleccion] = $resultado;

        return response()->json([
            'message'  => 'Xuxemon capturado',
            'xuxemon'  => $xuxemon,
            'coleccion' => $coleccion,
        ], 201);
    }

    public function storeForUser(Request $request, User $user)
    {
        if (!$user->esJugador()) {
            return response()->json([
                'message' => 'Solo se pueden asignar Xuxemons a jugadores',
            ], 422);
        }

        $resultado = $this->crearXuxemonAleatorioParaUsuario($user);

        if (!$resultado) {
            return response()->json(['message' => 'No hay xuxemons disponibles'], 404);
        }

        [$xuxemon, $coleccion] = $resultado;

        return response()->json([
            'message'  => 'Xuxemon aleatorio asignado correctamente',
            'xuxemon'  => $xuxemon,
            'coleccion' => $coleccion,
            'jugador'  => [
                'id'           => $user->id,
                'name'         => $user->name,
                'apellidos'    => $user->apellidos,
                'email'        => $user->email,
                'id_usuario'   => $user->id_usuario,
                'total_xuxemons' => $user->totalXuxemonsUnicosColeccion(),
            ],
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

    public function alimentar(Request $request, Coleccion $coleccion)
    {
        if ($coleccion->id_usuario != $request->user()->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if (!$coleccion->puedAlimentarse()) {
            return response()->json(['message' => 'El Xuxemon no puede alimentarse - está afectado por una enfermedad'], 400);
        }

        $mochilaXuxe = Mochila::where('id_usuario', $request->user()->id)
            ->whereHas('item', fn($q) => $q->where('tipo', 'xuxe'))
            ->where('cantidad', '>', 0)
            ->first();

        if (!$mochilaXuxe) {
            return response()->json(['message' => 'No tienes Xuxes en la mochila'], 400);
        }

        $mochilaXuxe->cantidad--;
        if ($mochilaXuxe->cantidad == 0) {
            $mochilaXuxe->delete();
        } else {
            $mochilaXuxe->save();
        }

        $coleccion->alimentaciones_pendientes++;
        $xuxesNecesarios = $coleccion->xuxesNecesariosParaCrecer();

        $config = ConfiguracionAdmin::obtener();
        $enfermedadesActuales = DB::table('xuxemon_enfermedad')
            ->where('coleccion_id', $coleccion->id)
            ->pluck('enfermedad_id')
            ->toArray();

        foreach (Enfermedad::all() as $enfermedad) {
            $porcentaje = $config->porcentajeInfeccion($enfermedad->nombre);
            if ($porcentaje > 0 && rand(1, 100) <= $porcentaje) {
                if (!in_array($enfermedad->id, $enfermedadesActuales)) {
                    DB::table('xuxemon_enfermedad')->insert([
                        'coleccion_id'  => $coleccion->id,
                        'enfermedad_id' => $enfermedad->id,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            }
        }

        $subeNivel = false;
        $xuxemonEvolucionado = null;

        if ($coleccion->alimentaciones_pendientes >= $xuxesNecesarios) {
            $coleccion->alimentaciones_pendientes -= $xuxesNecesarios;
            $coleccion->nivel++;

            if ($coleccion->tamaño_actual == 'Pequeño') {
                $coleccion->tamaño_actual = 'Mediano';
            } elseif ($coleccion->tamaño_actual == 'Mediano') {
                $coleccion->tamaño_actual = 'Grande';
            }

            $xuxemonActual = Xuxemon::with('evolucion')->find($coleccion->id_xuxemon);
            if ($xuxemonActual && $xuxemonActual->evolucion) {
                $coleccion->id_xuxemon = $xuxemonActual->evolucion->id;
                $xuxemonEvolucionado = $xuxemonActual->evolucion;
            }

            $subeNivel = true;
        }

        $coleccion->save();

        $enfermedadesActuales = DB::table('xuxemon_enfermedad')
            ->where('coleccion_id', $coleccion->id)
            ->join('enfermedades', 'xuxemon_enfermedad.enfermedad_id', '=', 'enfermedades.id')
            ->select('enfermedades.*')
            ->get();

        return response()->json([
            'message'              => $subeNivel ? 'El Xuxemon subió de nivel!' : 'Alimentado correctamente',
            'coleccion'            => $coleccion,
            'tamano_actual'        => $coleccion->tamaño_actual,
            'nivel'                => $coleccion->nivel,
            'enfermedades'         => $enfermedadesActuales,
            'subio_nivel'          => $subeNivel,
            'xuxemon_evolucionado' => $xuxemonEvolucionado,
        ], 200);
    }

    public function curar(Request $request, Coleccion $coleccion)
    {
        if ($coleccion->id_usuario != $request->user()->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'id_item' => 'required|exists:items,id',
        ]);

        $itemVacuna = Item::find($validated['id_item']);
        $vacuna = Vacuna::where('nombre', $itemVacuna->nombre)->first();

        if (!$vacuna) {
            return response()->json(['message' => 'Vacuna no trobada al sistema'], 404);
        }

        $mochilaVacuna = Mochila::where('id_usuario', $request->user()->id)
            ->where('id_item', $itemVacuna->id)
            ->first();

        if (!$mochilaVacuna || $mochilaVacuna->cantidad < 1) {
            return response()->json(['message' => 'No tienes esta vacuna en la mochila'], 400);
        }

        if ($vacuna->nombre == 'Inxulina') {
            $mochilaVacuna->cantidad--;
            if ($mochilaVacuna->cantidad <= 0) {
                $mochilaVacuna->delete();
            } else {
                $mochilaVacuna->save();
            }

            DB::table('xuxemon_enfermedad')
                ->where('coleccion_id', $coleccion->id)
                ->delete();
            $curadas = 'todas las enfermedades';
        } else {
            // Verificar que tiene esa enfermedad antes de consumir la vacuna
            $tieneEnfermedad = DB::table('xuxemon_enfermedad')
                ->where('coleccion_id', $coleccion->id)
                ->where('enfermedad_id', $vacuna->cura_enfermedad_id)
                ->exists();

            if (!$tieneEnfermedad) {
                return response()->json([
                    'message' => 'Este Xuxemon no tiene esa enfermedad',
                ], 422);
            }

            $mochilaVacuna->cantidad--;
            if ($mochilaVacuna->cantidad <= 0) {
                $mochilaVacuna->delete();
            } else {
                $mochilaVacuna->save();
            }

            DB::table('xuxemon_enfermedad')
                ->where('coleccion_id', $coleccion->id)
                ->where('enfermedad_id', $vacuna->cura_enfermedad_id)
                ->delete();
            $curadas = $vacuna->enfermedadCurada->nombre;
        }

        $enfermedadesRestantes = DB::table('xuxemon_enfermedad')
            ->where('coleccion_id', $coleccion->id)
            ->join('enfermedades', 'xuxemon_enfermedad.enfermedad_id', '=', 'enfermedades.id')
            ->select('enfermedades.*')
            ->get();

        return response()->json([
            'message'                => "Curado de $curadas",
            'coleccion'              => $coleccion,
            'enfermedades_restantes' => $enfermedadesRestantes,
        ], 200);
    }

    private function crearXuxemonAleatorioParaUsuario(User $user): ?array
    {
        $xuxemon = Xuxemon::where('tamaño', 'Pequeño')->inRandomOrder()->first();

        if (!$xuxemon) {
            return null;
        }

        $coleccion = Coleccion::create([
            'id_usuario'    => $user->id,
            'id_xuxemon'    => $xuxemon->id,
            'tamaño_actual' => 'Pequeño',
        ]);

        return [$xuxemon, $coleccion];
    }
}