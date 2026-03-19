<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Xuxemon extends Model
{
    protected $fillable = [
        'nombre',
        'tipo',
        'evolucion',
        'vida',
        'ataque',
        'defensa',
        'imagen'
    ];

    public function colecciones()
    {
        return $this->hasMany(Coleccion::class);
    }
}
