<?php

namespace App\Http\Controllers;

use App\Models\Xuxemon;
use Illuminate\Http\Request;

class XuxemonController extends Controller
{
    public function index()
    {
        return response()->json(Xuxemon::all(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|in:Agua,Tierra,Aire',
            'tamaño' => 'required|in:Pequeño,Mediano,Grande',
            'imagen' => 'nullable|string',
        ]);

        $xuxemon = Xuxemon::create($validated);
        return response()->json($xuxemon, 201);
    }

    public function show(Xuxemon $xuxemon)
    {
        return response()->json($xuxemon, 200);
    }
}

