<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mochila extends Model
{
    protected $table = 'mochila';
    protected $fillable = ['id_usuario', 'id_item', 'cantidad', 'slot'];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function articulo(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'id_item');
    }
}


