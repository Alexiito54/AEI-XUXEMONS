<?php

namespace App\Http\Controllers;

use App\Models\Xuxemon;
use Illuminate\Http\Request;

class XuxemonController extends Controller
{
    // GET /api/xuxemons
    public function index()
    {
        return response()->json(Xuxemon::all());
    }

    // GET /api/xuxemons/{id}
    public function show(string $id)
    {
        $xuxemon = Xuxemon::findOrFail($id);
        return response()->json($xuxemon);
    }

    // POST /api/xuxemons — solo admin
    public function store(Request $request)
    {
        $request->validate([
            'nombre'    => 'required|string|max:50',
            'tipo'      => 'required|in:fuego,agua,tierra,aire,electrico,normal',
            'evolucion' => 'required|in:base,media,final',
            'vida'      => 'required|integer|min:1',
            'ataque'    => 'required|integer|min:1',
            'defensa'   => 'required|integer|min:1',
            'imagen'    => 'nullable|string',
        ]);

        $xuxemon = Xuxemon::create($request->all());

        return response()->json([
            'success' => true,
            'xuxemon' => $xuxemon
        ], 201);
    }

    // PUT /api/xuxemons/{id} — solo admin
    public function update(Request $request, string $id)
    {
        $xuxemon = Xuxemon::findOrFail($id);

        $request->validate([
            'nombre'    => 'sometimes|string|max:50',
            'tipo'      => 'sometimes|in:fuego,agua,tierra,aire,electrico,normal',
            'evolucion' => 'sometimes|in:base,media,final',
            'vida'      => 'sometimes|integer|min:1',
            'ataque'    => 'sometimes|integer|min:1',
            'defensa'   => 'sometimes|integer|min:1',
            'imagen'    => 'nullable|string',
        ]);

        $xuxemon->update($request->all());

        return response()->json([
            'success' => true,
            'xuxemon' => $xuxemon
        ]);
    }

    // DELETE /api/xuxemons/{id} — solo admin
    public function destroy(string $id)
    {
        Xuxemon::findOrFail($id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'Xuxemon eliminado'
        ]);
    }
}
