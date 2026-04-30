<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vacuna extends Model
{
    protected $table = 'vacunas';
    protected $fillable = ['nombre', 'cura_enfermedad_id'];

    /**
     * Obtener la enfermedad que esta vacuna cura.
     * 1 Vacuna -> 1 Enfermedad
     */
    public function enfermedadCurada(): BelongsTo
    {
        return $this->belongsTo(Enfermedad::class, 'cura_enfermedad_id');
    }

    /**
     * Obtener el item asociado a esta vacuna (si existe).
     * Las vacunas se guardan como items en el inventario.
     * 1 Vacuna -> 1 Item (si es necesario)
     */
    public function item(): BelongsTo
    {
        // Esta relación asume que existe un campo id_vacuna en la tabla items
        // O puedes usar una tabla pivot si quieres una relación N:N
        return $this->belongsTo(Item::class, 'id', 'id_vacuna');
    }
}
