<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coleccion extends Model
{
    protected $table = 'colecciones';
    protected $fillable = ['id_usuario', 'id_xuxemon', 'tamaño_actual', 'nivel', 'alimentaciones_pendientes'];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function xuxemon(): BelongsTo
    {
        return $this->belongsTo(Xuxemon::class, 'id_xuxemon');
    }
    public function enfermedades()
    {
        return $this->belongsToMany(
            Enfermedad::class,
            'xuxemon_enfermedad',
            'coleccion_id',
            'enfermedad_id'
        );
    }
}


