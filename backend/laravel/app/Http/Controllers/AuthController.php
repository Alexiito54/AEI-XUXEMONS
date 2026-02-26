<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

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


        $user = User::create([
            'user_id'  => $userId,      
            'name'     => $request->name,
            'surname'  => $request->surname,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'user_id' => $user->user_id, 
            'message' => 'Usuario registrado'
        ], 201);
    }
}
