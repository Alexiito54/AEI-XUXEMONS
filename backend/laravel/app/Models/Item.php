<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'items';

    protected $fillable = [
        'nombre',
        'tipo',
        'icono',
        'apilable',
    ];

    public function mochila()
    {
        return $this->hasMany(Mochila::class, 'item_id');
    }
}
