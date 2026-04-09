<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vacuna extends Model
{
    protected $table = 'vacunas';
    protected $fillable = ['nombre', 'cura_enfermedad_id'];

    public function enfermedadCurada(): BelongsTo
    {
        return $this->belongsTo(Enfermedad::class, 'cura_enfermedad_id');
    }
}
