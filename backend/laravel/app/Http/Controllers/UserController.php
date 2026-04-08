<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
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
