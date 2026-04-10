<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionAdmin;
use Illuminate\Http\Request;

class ConfiguracionAdminController extends Controller
{
    public function show()
    {
        $config = ConfiguracionAdmin::obtener();
        return response()->json($config, 200);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'xuxes_pequeno_mediano' => 'integer|min:1',
            'xuxes_mediano_grande' => 'integer|min:1',
            'porcentaje_bajón' => 'integer|min:0|max:100',
            'porcentaje_sobredosis' => 'integer|min:0|max:100',
            'porcentaje_atracón' => 'integer|min:0|max:100',
            'hora_xuxes_diarias' => 'date_format:H:i',
            'cantidad_xuxes_diarias' => 'integer|min:1',
            'hora_xuxemon_diario' => 'date_format:H:i',
        ]);

        $config = ConfiguracionAdmin::first();
        $config->update($validated);

        return response()->json($config, 200);
    }
}
