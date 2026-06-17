<?php

namespace App\Http\Controllers;

use App\Http\Controllers\PerfilController;
use App\Models\User;
use App\Models\Item;
use App\Models\Mochila;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'      => 'required|string|max:255',
                'apellidos' => 'required|string|max:255',
                'email'     => 'required|string|email|max:255|unique:users',
                'password'  => 'required|string|min:6|confirmed',
            ]);

            $user = User::create([
                'name'      => $validated['name'],
                'apellidos' => $validated['apellidos'],
                'email'     => $validated['email'],
                'password'  => Hash::make($validated['password']),
            ]);

            // ── Items inicials aleatoris
            $this->darItemsInicials($user->id);

            return response()->json([
                'message' => 'Registro exitoso',
                'user' => [
                    'id'         => $user->id,
                    'name'       => $user->name,
                    'apellidos'  => $user->apellidos,
                    'email'      => $user->email,
                    'id_usuario' => $user->id_usuario,
                    'rol'        => $user->rol,
                ],
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error en la validación',
                'errors'  => $e->errors(),
            ], 422);
        }
    }

    private function darItemsInicials(int $userId): void
    {
        $xuxes   = Item::where('tipo', 'xuxe')->get();
        $vacunes = Item::where('tipo', 'vacuna')->get();

        // Si no hi ha items a la BD, no fem res
        if ($xuxes->isEmpty()) return;

        $itemsAInserir = [];
        $slot = 1;

        foreach ($xuxes as $xuxa) {
            if ($slot > 20) break;
            $itemsAInserir[] = [
                'id_usuario' => $userId,
                'id_item'    => $xuxa->id,
                'cantidad'   => rand(1, 5),
                'slot'       => $slot++,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // 1 vacuna aleatòria
        if ($slot <= 20 && $vacunes->isNotEmpty()) {
            $vacuna = $vacunes->random();
            $itemsAInserir[] = [
                'id_usuario' => $userId,
                'id_item'    => $vacuna->id,
                'cantidad'   => 1,
                'slot'       => $slot,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Mochila::insert($itemsAInserir);
    }

    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_usuario' => 'required|string',
                'password'   => 'required|string',
            ]);

            $user = User::where('id_usuario', $validated['id_usuario'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'message' => 'Credenciales inválidas',
                ], 401);
            }

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'message' => 'Inicio de sesión exitoso',
                'token'   => $token,
                'user'    => [
                    'id'         => $user->id,
                    'name'       => $user->name,
                    'apellidos'  => $user->apellidos,
                    'email'      => $user->email,
                    'id_usuario' => $user->id_usuario,
                    'rol'        => $user->rol,
                ],
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error en la validación',
                'errors'  => $e->errors(),
            ], 422);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Cierre de sesión exitoso',
        ], 200);
    }
}