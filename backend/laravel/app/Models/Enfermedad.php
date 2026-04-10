<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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

    public function colecciones(): HasMany
    {
        return $this->hasMany(Coleccion::class, 'enfermedad_id');
    }
}
