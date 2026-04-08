<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
    {
        return response()->json(Item::all(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|in:xuxe,vacuna',
            'icono' => 'required|string',
            'apilable' => 'boolean|default:true',
        ]);

        $item = Item::create($validated);
        return response()->json($item, 201);
    }

    public function show(Item $item)
    {
        return response()->json($item, 200);
    }
}

