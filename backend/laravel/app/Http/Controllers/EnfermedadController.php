<?php

namespace App\Http\Controllers;

use App\Models\Enfermedad;
use Illuminate\Http\Request;

class EnfermedadController extends Controller
{
    public function index()
    {
        return response()->json(Enfermedad::all(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|unique:enfermedades',
            'descripcion' => 'nullable|string',
            'xuxes_extra_para_crecer' => 'integer|min:0',
            'impide_alimentacion' => 'boolean',
            'porcentaje_infeccion' => 'integer|min:0|max:100',
        ]);

        $enfermedad = Enfermedad::create($validated);
        return response()->json($enfermedad, 201);
    }

    public function show(Enfermedad $enfermedad)
    {
        return response()->json($enfermedad, 200);
    }
}
