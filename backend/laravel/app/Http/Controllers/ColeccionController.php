<?php

namespace App\Http\Controllers;

use App\Models\Coleccion;
use App\Models\Xuxemon;
use App\Models\Mochila;
use App\Models\Item;
use App\Models\Enfermedad;
use App\Models\Vacuna;
use App\Models\ConfiguracionAdmin;
use App\Models\User;
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

    /**
     * Obtener datos de la Xuxedex según el rol del usuario
     * Admin: Ve todos los Xuxemons como atrapados
     * Usuario normal: Ve algunos como atrapados, otros ocultos
     */
    public function xuxedex(Request $request)
    {
        $user = $request->user();
        $isAdmin = $user->esAdmin();

        // Obtener todos los Xuxemons disponibles
        $todosXuxemons = Xuxemon::all();

        // Obtener los Xuxemons capturados y cuantas copias tiene de cada uno
        $capturasPorXuxemon = Coleccion::where('id_usuario', $user->id)
            ->select('id_xuxemon', DB::raw('COUNT(*) as total'))
            ->groupBy('id_xuxemon')
            ->pluck('total', 'id_xuxemon');
        $xuxemonsCapturados = $capturasPorXuxemon->keys()->map(fn ($id) => (int) $id)->all();

        $resultado = [];

        if ($isAdmin) {
            // ADMIN: Todos los Xuxemons aparecen como atrapados y vistos
            foreach ($todosXuxemons as $xuxemon) {
                $resultado[] = [
                    'id' => $xuxemon->id,
                    'nombre' => $xuxemon->nombre,
                    'tipo' => $xuxemon->tipo,
                    'tamaño' => $xuxemon->tamaño,
                    'imagen' => $xuxemon->imagen,
                    'cantidad_capturada' => (int) ($capturasPorXuxemon->get($xuxemon->id, 0)),
                    'atrapado' => true,  // Todos atrapados para admin
                    'visto' => true,     // Todos vistos para admin
                    'oculto' => false,   // Ninguno oculto para admin
                ];
            }
        } else {
            // USUARIO NORMAL: Solo algunos aparecen como atrapados
            $totalXuxemons = $todosXuxemons->count();
            $mitad = (int) ceil($totalXuxemons / 2); // La mitad, redondeado hacia arriba

            // Seleccionar aleatoriamente cuáles mostrar como atrapados
            // Para consistencia, usar el ID del usuario como semilla
            $xuxemonsDisponibles = $todosXuxemons->pluck('id')->toArray();
            mt_srand($user->id); // Semilla consistente por usuario
            shuffle($xuxemonsDisponibles);
            $xuxemonsVisibles = array_slice($xuxemonsDisponibles, 0, $mitad);

            foreach ($todosXuxemons as $xuxemon) {
                $estaAtrapado = in_array($xuxemon->id, $xuxemonsCapturados);
                $estaVisible = in_array($xuxemon->id, $xuxemonsVisibles);

                // Si está atrapado, siempre debe mostrarse como visto y no oculto
                $esVisto = $estaVisible || $estaAtrapado;
                $esOculto = !$esVisto;

                $resultado[] = [
                    'id' => $xuxemon->id,
                    'nombre' => $xuxemon->nombre,
                    'tipo' => $xuxemon->tipo,
                    'tamaño' => $xuxemon->tamaño,
                    'imagen' => $xuxemon->imagen,
                    'cantidad_capturada' => (int) ($capturasPorXuxemon->get($xuxemon->id, 0)),
                    'atrapado' => $estaAtrapado,
                    'visto' => $esVisto,
                    'oculto' => $esOculto,
                ];
            }
        }

        return response()->json([
            'xuxemons' => $resultado,
            'estadisticas' => [
                'total' => $todosXuxemons->count(),
                'atrapados' => $isAdmin ? $todosXuxemons->count() : count(array_filter($resultado, fn($x) => $x['atrapado'])),
                'vistos' => $isAdmin ? $todosXuxemons->count() : count(array_filter($resultado, fn($x) => $x['visto'])),
                'is_admin' => $isAdmin,
            ]
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
            'message' => 'Xuxemon capturado',
            'xuxemon' => $xuxemon,
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
            'message' => 'Xuxemon aleatorio asignado correctamente',
            'xuxemon' => $xuxemon,
            'coleccion' => $coleccion,
            'jugador' => [
                'id' => $user->id,
                'name' => $user->name,
                'apellidos' => $user->apellidos,
                'email' => $user->email,
                'id_usuario' => $user->id_usuario,
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
        // Verificar autorización
        if ($coleccion->id_usuario != $request->user()->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Verificar si tiene enfermedad que impide alimentación
        $enfermedadesImpiden = DB::table('xuxemon_enfermedad')
            ->where('coleccion_id', $coleccion->id)
            ->join('enfermedades', 'xuxemon_enfermedad.enfermedad_id', '=', 'enfermedades.id')
            ->where('enfermedades.impide_alimentacion', true)
            ->exists();

        if ($enfermedadesImpiden) {
            return response()->json(['message' => 'El Xuxemon no puede alimentarse - está afectado por una enfermedad'], 400);
        }

        // Obtener item Xuxe de mochila
        $xuxeItem = Item::where('tipo', 'xuxe')->first();
        if (!$xuxeItem) {
            return response()->json(['message' => 'Item de Xuxe no encontrado'], 500);
        }

        $mochilaXuxe = Mochila::where('id_usuario', $request->user()->id)
            ->where('id_item', $xuxeItem->id)
            ->first();

        if (!$mochilaXuxe || $mochilaXuxe->cantidad < 1) {
            return response()->json(['message' => 'No tienes Xuxes en la mochila'], 400);
        }

        // Disminuir cantidad de Xuxes
        $mochilaXuxe->cantidad--;
        if ($mochilaXuxe->cantidad == 0) {
            $mochilaXuxe->delete();
        } else {
            $mochilaXuxe->save();
        }

        // Incrementar alimentaciones pendientes
        $coleccion->alimentaciones_pendientes++;

        // Obtener configuración
        $config = ConfiguracionAdmin::obtener();
       $xuxesNecesarios = ($coleccion->tamaño_actual == 'Pequeño') 
            ? $config->xuxes_pequeno_mediano 
            : $config->xuxes_mediano_grande;

        $tesBajonAzucar = DB::table('xuxemon_enfermedad')
            ->where('coleccion_id', $coleccion->id)
            ->join('enfermedades', 'xuxemon_enfermedad.enfermedad_id', '=', 'enfermedades.id')
            ->where('enfermedades.nombre', 'Bajón de azúcar')
            ->exists();

        if ($tesBajonAzucar) {
            $xuxesNecesarios += 2;
        }

        // Calcular enfermedades existentes
        $enfermedades = DB::table('xuxemon_enfermedad')
            ->where('coleccion_id', $coleccion->id)
            ->pluck('enfermedad_id')
            ->toArray();

        // Crear enfermedad aleatorio
        $todasEnfermedades = Enfermedad::all();
        foreach ($todasEnfermedades as $enfermedad) {
            $chance = rand(1, 100);
            if ($chance <= $enfermedad->porcentaje_infeccion) {
                if (!in_array($enfermedad->id, $enfermedades)) {
                    DB::table('xuxemon_enfermedad')->insert([
                        'coleccion_id' => $coleccion->id,
                        'enfermedad_id' => $enfermedad->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Verificar si sube de tamaño
        $subeNivel = false;
        if ($coleccion->alimentaciones_pendientes >= $xuxesNecesarios) {
            $coleccion->alimentaciones_pendientes -= $xuxesNecesarios;
            $coleccion->nivel++;

            if ($coleccion->tamaño_actual == 'Pequeño') {
                $coleccion->tamaño_actual = 'Mediano';
            } elseif ($coleccion->tamaño_actual == 'Mediano') {
                $coleccion->tamaño_actual = 'Grande';
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
            'message' => $subeNivel ? 'El Xuxemon subió de nivel!' : 'Alimentado correctamente',
            'coleccion' => $coleccion,
            'tamaño_actual' => $coleccion->tamaño_actual,
            'nivel' => $coleccion->nivel,
            'enfermedades' => $enfermedadesActuales,
            'subio_nivel' => $subeNivel,
        ], 200);
    }

    public function curar(Request $request, Coleccion $coleccion)
    {
        // Verificar autorización
        if ($coleccion->id_usuario != $request->user()->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'id_item' => 'required|exists:items,id', 
        ]);

        $vacuna = Vacuna::find($validated['id_vacuna']);

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

        // Eliminar vacuna de mochila
        $mochilaVacuna->delete();

        if ($vacuna->nombre == 'Inxulina') {
            // Cura todas las enfermedades
            DB::table('xuxemon_enfermedad')
                ->where('coleccion_id', $coleccion->id)
                ->delete();
            $curadas = 'todas las enfermedades';
        } else {
            // Cura enfermedad específica
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
            'message' => "Curado de $curadas",
            'coleccion' => $coleccion,
            'enfermedades_restantes' => $enfermedadesRestantes,
        ], 200);
    }

    private function crearXuxemonAleatorioParaUsuario(User $user): ?array
    {
        $xuxemon = Xuxemon::inRandomOrder()->first();

        if (!$xuxemon) {
            return null;
        }

        $coleccion = Coleccion::create([
            'id_usuario' => $user->id,
            'id_xuxemon' => $xuxemon->id,
        ]);

        return [$xuxemon, $coleccion];
    }
}
