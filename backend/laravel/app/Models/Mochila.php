<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mochila extends Model
{
    protected $table = 'mochila';
    protected $fillable = ['id_usuario', 'id_item', 'cantidad', 'slot'];

    /**
     * Obtener el usuario propietario de este slot en la mochila.
     * N Items en Mochila -> 1 Usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    /**
     * Obtener el item almacenado en este slot de la mochila.
     * 1 Slot Mochila -> 1 Item
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'id_item');
    }

    /**
     * Alias para compatibilidad con código existente.
     * @deprecated Usar item() en su lugar
     */
    public function articulo(): BelongsTo
    {
        return $this->item();
    }
}


