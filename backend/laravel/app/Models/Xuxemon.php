<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Xuxemon extends Model
{
    protected $table = 'xuxemons';
    protected $fillable = ['nombre', 'tipo', 'tamaño', 'imagen'];

    /**
     * Obtener todas las colecciones (instancias) de este Xuxemon.
     * Un Xuxemon puede estar en muchas colecciones de diferentes usuarios.
     * 1 Xuxemon -> N Colecciones
     */
    public function colecciones(): HasMany
    {
        return $this->hasMany(Coleccion::class, 'id_xuxemon');
    }
}
