<?php

namespace App\Http\Controllers;

use App\Models\Coleccion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Get all registered players for the admin panel.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexJugadores()
    {
        $jugadores = User::query()
            ->where('rol', 'jugador')
            ->addSelect([
                'total_xuxemons' => Coleccion::query()
                    ->selectRaw('COUNT(DISTINCT id_xuxemon)')
                    ->whereColumn('id_usuario', 'users.id'),
            ])
            ->orderBy('name')
            ->orderBy('apellidos')
            ->get([
                'id',
                'name',
                'apellidos',
                'email',
                'id_usuario',
                'rol',
            ]);

        return response()->json([
            'jugadores' => $jugadores,
        ], 200);
    }

    /**
     * Get the authenticated user's profile.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getUser(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'apellidos' => $user->apellidos,
                'email' => $user->email,
                'id_usuario' => $user->id_usuario,
                'rol' => $user->rol,
            ],
        ], 200);
    }

    /**
     * Update the authenticated user's profile.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateUser(Request $request)
    {
        try {
            $user = $request->user();

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'apellidos' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            ]);

            $user->update($validated);

            return response()->json([
                'message' => 'Perfil actualizado correctamente',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'apellidos' => $user->apellidos,
                    'email' => $user->email,
                    'id_usuario' => $user->id_usuario,
                    'rol' => $user->rol,
                ],
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error en la validación',
                'errors' => $e->errors(),
            ], 422);
        }
    }

public function perfilStats(Request $request)
{
    $user = $request->user();

    $totalXuxemons = Coleccion::where('id_usuario', $user->id)
    ->distinct('id_xuxemon')
    ->count('id_xuxemon');

    $xuxemonsPorTipo = Coleccion::where('id_usuario', $user->id)
    ->join('xuxemons', 'colecciones.id_xuxemon', '=', 'xuxemons.id')
    ->selectRaw('xuxemons.tipo, COUNT(DISTINCT colecciones.id_xuxemon) as total')
    ->groupBy('xuxemons.tipo')
    ->get()
    ->map(fn($item) => [
        'tipo'  => $item->tipo,
        'total' => $item->total,
        'icono' => match($item->tipo) {
            'Agua'   => '💧',
            'Tierra' => '🪨',
            'Aire'   => '💨',
            default  => '❓',
        },
    ]);

    $nivel = min(50, (int) floor($totalXuxemons / 5) + 1);
    $xpActual = $totalXuxemons % 5;
    $xpSiguiente = 5;

    return response()->json([
        'total_xuxemons'      => $totalXuxemons,
        'total_batallas'      => 0,
        'total_amigos'        => 0,
        'nivel'               => $nivel,
        'xp_actual'           => $xpActual,
        'xp_siguiente_nivel'  => $xpSiguiente,
        'porcentaje_victorias' => 0,
        'xuxemons_por_tipo'   => $xuxemonsPorTipo,
        'preferencias'        => [
            'notificaciones_batalla' => false,
            'perfil_publico'         => false,
            'mensajes_privados'      => false,
        ],
    ], 200);
}


    /**
     * Change the user's password.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
        try {
            $user = $request->user();

            $validated = $request->validate([
                'current_password' => 'required|string',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if (!Hash::check($validated['current_password'], $user->password)) {
                return response()->json([
                    'message' => 'La contraseña actual es incorrecta',
                ], 401);
            }

            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            return response()->json([
                'message' => 'Contraseña cambiada correctamente',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error en la validación',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Delete the user account.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteUser(Request $request)
    {
        try {
            $user = $request->user();

            $validated = $request->validate([
                'password' => 'required|string',
            ]);

            if (!Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'message' => 'La contraseña es incorrecta',
                ], 401);
            }

            $user->delete();

            return response()->json([
                'message' => 'Cuenta eliminada correctamente',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error en la validación',
                'errors' => $e->errors(),
            ], 422);
        }
    }
}
