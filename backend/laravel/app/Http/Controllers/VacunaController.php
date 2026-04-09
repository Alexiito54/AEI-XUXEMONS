<?php

namespace App\Http\Controllers;

use App\Models\Vacuna;
use Illuminate\Http\Request;

class VacunaController extends Controller
{
    public function index()
    {
        return response()->json(Vacuna::with('enfermedadCurada')->get(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|unique:vacunas',
            'cura_enfermedad_id' => 'required|exists:enfermedades,id',
        ]);

        $vacuna = Vacuna::create($validated);
        return response()->json($vacuna->load('enfermedadCurada'), 201);
    }

    public function show(Vacuna $vacuna)
    {
        return response()->json($vacuna->load('enfermedadCurada'), 200);
    }
}
