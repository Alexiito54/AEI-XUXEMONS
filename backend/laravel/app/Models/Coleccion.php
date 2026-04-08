<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Coleccion extends Model
{
    protected $table = 'colecciones';
    protected $fillable = ['id_usuario', 'id_xuxemon'];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function xuxemon(): BelongsTo
    {
        return $this->belongsTo(Xuxemon::class, 'id_xuxemon');
    }
}


