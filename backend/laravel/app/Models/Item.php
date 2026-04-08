<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = ['nombre', 'tipo', 'icono', 'apilable'];

    public function mochilas(): HasMany
    {
        return $this->hasMany(Mochila::class);
    }
}

