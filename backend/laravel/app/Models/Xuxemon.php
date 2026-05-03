<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Xuxemon extends Model
{
    protected $table = 'xuxemons';
    protected $fillable = ['nombre', 'tipo', 'tamaño', 'imagen', 'evoluciona_a'];

    public function colecciones(): HasMany
    {
        return $this->hasMany(Coleccion::class, 'id_xuxemon');
    }

    // El Xuxemon al que evoluciona
    public function evolucion(): BelongsTo
    {
        return $this->belongsTo(Xuxemon::class, 'evoluciona_a');
    }
}