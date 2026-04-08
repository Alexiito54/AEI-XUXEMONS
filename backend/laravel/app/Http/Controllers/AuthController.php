<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'apellidos' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'apellidos' => $validated['apellidos'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            return response()->json([
                'message' => 'Registro exitoso',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'apellidos' => $user->apellidos,
                    'email' => $user->email,
                    'id_usuario' => $user->id_usuario,
                    'rol' => $user->rol,
                ],
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error en la validación',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Login a user.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_usuario' => 'required|string',
                'password' => 'required|string',
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
                'token' => $token,
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

    /**
     * Logout a user.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Cierre de sesión exitoso',
        ], 200);
    }
}
}
