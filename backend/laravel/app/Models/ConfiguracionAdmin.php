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

    /**
     * Obtener la configuración global (singleton pattern).
     * Existe una única configuración para toda la aplicación.
     */
    public static function obtener()
    {
        return self::first() ?? self::create([
            'xuxes_pequeno_mediano' => 3,
            'xuxes_mediano_grande' => 5,
            'porcentaje_bajón' => 5,
            'porcentaje_sobredosis' => 10,
            'porcentaje_atracón' => 15,
            'hora_xuxes_diarias' => '08:00',
            'cantidad_xuxes_diarias' => 10,
            'hora_xuxemon_diario' => '08:00',
        ]);
    }

    /**
     * Obtener los xuxes necesarios para evolucionar según tamaño actual.
     */
    public function xuxesParaEvolucionar(string $tamañoActual): int
    {
        return match ($tamañoActual) {
            'Pequeño' => $this->xuxes_pequeno_mediano,
            'Mediano' => $this->xuxes_mediano_grande,
            default => 999, // No puede evolucionar desde Grande
        };
    }

    /**
     * Obtener el porcentaje de infección según enfermedad.
     */
    public function porcentajeInfeccion(string $nombreEnfermedad): int
    {
        return match ($nombreEnfermedad) {
            'Bajón de azúcar' => $this->porcentaje_bajón,
            'Sobredosis de sucre' => $this->porcentaje_sobredosis,
            'Atracón' => $this->porcentaje_atracón,
            default => 0,
        };
    }
}
