<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Xuxemon extends Model
{
    protected $table = 'xuxemons';
    protected $fillable = ['nombre', 'tipo', 'tamaño', 'imagen'];

    public function colecciones(): HasMany
    {
        return $this->hasMany(Coleccion::class, 'id_xuxemon');
    }
}
