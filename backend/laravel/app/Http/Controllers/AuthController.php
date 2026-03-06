<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:50',
            'surname'  => 'required|string|max:100',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $nom    = str_replace(' ', '', $request->name);
        $num    = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $userId = '#' . $nom . $num;

        $role = User::count() === 0 ? 'admin' : 'player';

        $user = User::create([
            'user_id'  => $userId,
            'name'     => $request->name,
            'surname'  => $request->surname,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $role,
        ]);

        return response()->json([
            'success' => true,
            'user_id' => $user->user_id,
            'message' => 'Usuario registrado'
        ], 201);
    }
public function login(Request $request)
{
    $request->validate([
        'user_id'  => 'required|string',
        'password' => 'required|string|min:6',
    ]);

    $user = User::where('user_id', $request->user_id)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'Credencials incorrectes'
        ], 401);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user'  => [
            'user_id' => $user->user_id,
            'name'    => $user->name,
            'role'    => $user->role
        ]
    ]);
}

public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logout exitós']);
}
}

