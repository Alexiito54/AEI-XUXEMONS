<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ConfiguracionAdmin;
use App\Models\Coleccion;
use App\Models\Mochila;
use App\Models\Item;
use App\Models\Xuxemon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiarioController extends Controller
{
    public function reclamarXuxesDiarios(Request $request)
    {
        $user = $request->user();
        $hoy = now()->toDateString();

        // Verificar si ya reclamo hoy
        $yaReclamo = DB::table('xuxes_diarias_log')
            ->where('id_usuario', $user->id)
            ->where('fecha_reclamacion', $hoy)
            ->exists();

        if ($yaReclamo) {
            return response()->json(['message' => 'Ya reclamaste los Xuxes hoy'], 400);
        }

        $config = ConfiguracionAdmin::obtener();
        $cantidad = $config->cantidad_xuxes_diarias ?? 10;

        // Obtener item de Xuxe
        $xuxeItem = Item::where('tipo', 'xuxe')->first();

        if (!$xuxeItem) {
            return response()->json(['message' => 'Item de Xuxe no encontrado'], 500);
        }

        // Crear entrada en mochila con $cantidad xuxes
        for ($i = 0; $i < $cantidad; $i++) {
            Mochila::create([
                'id_usuario' => $user->id,
                'id_item' => $xuxeItem->id,
                'cantidad' => 1,
            ]);
        }

        // Registrar en log
        DB::table('xuxes_diarias_log')->insert([
            'id_usuario' => $user->id,
            'cantidad_recibida' => $cantidad,
            'fecha_reclamacion' => $hoy,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => "¡Recibiste $cantidad Xuxes!",
            'cantidad' => $cantidad,
        ], 200);
    }

    public function reclamarXuxemonDiario(Request $request)
    {
        $user = $request->user();
        $hoy = now()->toDateString();

        // Verificar si ya reclamo hoy
        $yaReclamo = DB::table('xuxemon_diario_log')
            ->where('id_usuario', $user->id)
            ->where('fecha_reclamacion', $hoy)
            ->exists();

        if ($yaReclamo) {
            return response()->json(['message' => 'Ya reclamaste el Xuxemon hoy'], 400);
        }

        // Obtener Xuxemon aleatorio
        $xuxemon = Xuxemon::inRandomOrder()->first();

        if (!$xuxemon) {
            return response()->json(['message' => 'No hay Xuxemons disponibles'], 404);
        }

        // Crear en colecciones
        $coleccion = Coleccion::create([
            'id_usuario' => $user->id,
            'id_xuxemon' => $xuxemon->id,
            'tamaño_actual' => 'Pequeño',
            'nivel' => 0,
            'alimentaciones_pendientes' => 0,
        ]);

        // Registrar en log
        DB::table('xuxemon_diario_log')->insert([
            'id_usuario' => $user->id,
            'id_xuxemon' => $xuxemon->id,
            'fecha_reclamacion' => $hoy,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => '¡Recibiste un Xuxemon!',
            'xuxemon' => $xuxemon,
            'coleccion' => $coleccion,
        ], 201);
    }
}
