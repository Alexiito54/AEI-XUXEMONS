<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //listar todos los entrenadores (solo admin)
    public function index()
    {
        $users = User::select('user_id', 'name', 'surname', 'email', 'role', 'created_at')
            ->get();

        return response()->json($users);
    }

    // ver perfil de un entrenador
    public function show($user_id)
    {
        $user = User::where('user_id', $user_id)
            ->select('user_id', 'name', 'surname', 'email', 'role', 'created_at')
            ->firstOrFail();

        return response()->json($user);
    }

    // editar entrenador (solo admin)
    public function update(Request $request, $user_id)
    {
        $user = User::where('user_id', $user_id)->firstOrFail();

        $request->validate([
            'name'     => 'sometimes|string|max:50',
            'surname'  => 'sometimes|string|max:100',
            'email'    => 'sometimes|email|unique:users,email,' . $user->id,
            'role'     => 'sometimes|in:admin,player',
            'password' => 'sometimes|min:6|confirmed',
        ]);

        $data = $request->only(['name', 'surname', 'email', 'role']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'user'    => $user->only(['user_id', 'name', 'surname', 'email', 'role'])
        ]);
    }

    // eliminar entrenador (solo admin)
    public function destroy($user_id)
    {
        $user = User::where('user_id', $user_id)->firstOrFail();

        // Evitar que el admin se elimine a sí mismo
        if (auth()->user()->user_id === $user_id) {
            return response()->json([
                'message' => 'No puedes eliminarte a ti mismo'
            ], 422);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Entrenador eliminado'
        ]);
    }
}
