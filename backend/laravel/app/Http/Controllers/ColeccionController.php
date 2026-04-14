<?php

namespace App\Http\Controllers;

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
        $xuxesNecesarios = ($coleccion->tamaño_actual == 'Pequeño') ? $config->xuxes_pequeno_mediano : $config->xuxes_mediano_grande;

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
            'id_vacuna' => 'required|exists:vacunas,id',
        ]);

        $vacuna = Vacuna::find($validated['id_vacuna']);

        // Verificar si tiene vacuna en mochila
        $itemVacuna = Item::where('tipo', 'vacuna')
            ->where('nombre', $vacuna->nombre)
            ->first();

        if (!$itemVacuna) {
            return response()->json(['message' => 'Vacuna no existe como item'], 500);
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
}

