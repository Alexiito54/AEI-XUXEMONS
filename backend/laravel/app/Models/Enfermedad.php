<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enfermedad extends Model
{
    protected $table = 'enfermedades';
    protected $fillable = [
        'nombre',
        'descripcion',
        'xuxes_extra_para_crecer',
        'impide_alimentacion',
        'porcentaje_infeccion',
    ];

    /**
     * Obtener todas las colecciones (Xuxemons) que tienen esta enfermedad.
     * Una enfermedad puede afectar a N Xuxemons de diferentes usuarios.
     * N Enfermedades -> N Colecciones (Many to Many)
     */
    public function colecciones(): BelongsToMany
    {
        return $this->belongsToMany(Coleccion::class, 'xuxemon_enfermedad', 'enfermedad_id', 'coleccion_id')
                    ->withTimestamps()
                    ->withPivot('fecha_contagio');
    }

    /**
     * Obtener todas las vacunas que curan esta enfermedad.
     * Una enfermedad puede ser curada por varias vacunas.
     * 1 Enfermedad -> N Vacunas
     */
    public function vacunas(): HasMany
    {
        return $this->hasMany(Vacuna::class, 'cura_enfermedad_id');
    }
}
