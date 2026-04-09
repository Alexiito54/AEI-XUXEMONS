<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionAdmin extends Model
{
    protected $table = 'configuracion_admin';
    protected $fillable = [
        'xuxes_pequeno_mediano',
        'xuxes_mediano_grande',
        'porcentaje_bajón',
        'porcentaje_sobredosis',
        'porcentaje_atracón',
        'hora_xuxes_diarias',
        'cantidad_xuxes_diarias',
        'hora_xuxemon_diario',
    ];

    public static function obtener()
    {
        return self::first() ?? self::create([]);
    }
}
