<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    protected $fillable = ['nombre', 'tipo', 'icono', 'apilable'];

    /**
     * Obtener todas las instancias de este item en las mochilas de los usuarios.
     * 1 Item -> N Mochilas
     */
    public function mochilas(): HasMany
    {
        return $this->hasMany(Mochila::class, 'id_item');
    }

    /**
     * Si este item es una vacuna, obtener la relación.
     * Nota: Esto asume que existe id_vacuna en items o una tabla pivot.
     * 1 Item Vacuna -> 1 Vacuna
     */
    public function vacuna(): BelongsTo
    {
        // Asume que existe un campo id_vacuna en la tabla items
        return $this->belongsTo(Vacuna::class, 'id_vacuna');
    }

    /**
     * Scope para obtener solo items de tipo xuxe (comida).
     */
    public function scopeXuxes($query)
    {
        return $query->where('tipo', 'xuxe');
    }

    /**
     * Scope para obtener solo items de tipo vacuna.
     */
    public function scopeVacunas($query)
    {
        return $query->where('tipo', 'vacuna');
    }

    /**
     * Scope para obtener solo items apilables.
     */
    public function scopeApilables($query)
    {
        return $query->where('apilable', true);
    }

    /**
     * Scope para obtener solo items no apilables.
     */
    public function scopeNoApilables($query)
    {
        return $query->where('apilable', false);
    }
}

