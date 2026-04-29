<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coleccion extends Model
{
    protected $table = 'colecciones';
    protected $fillable = [
        'id_usuario',
        'id_xuxemon',
        'tamaño_actual',
        'nivel',
        'alimentaciones_pendientes',
        'capturado_en'
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function xuxemon(): BelongsTo
    {
        return $this->belongsTo(Xuxemon::class, 'id_xuxemon');
    }

    public function enfermedades(): BelongsToMany
    {
        return $this->belongsToMany(Enfermedad::class, 'xuxemon_enfermedad', 'coleccion_id', 'enfermedad_id')
                    ->withTimestamps()
                    ->withPivot('fecha_contagio');
    }

    public function estaEnfermo(): bool
    {
        return $this->enfermedades()->exists();
    }

    public function tieneEnfermedad(string $nombreEnfermedad): bool
    {
        return $this->enfermedades()
                    ->where('nombre', $nombreEnfermedad)
                    ->exists();
    }

    public function puedAlimentarse(): bool
    {
        return !$this->tieneEnfermedad('Atracón');
    }

    public function xuxesNecesariosParaCrecer(): int
    {
        $base = match ($this->tamaño_actual) {
            'Pequeño' => 3,
            'Mediano' => 5,
            default   => 999,
        };

        if ($this->tieneEnfermedad('Bajón de azúcar')) {
            $base += 2;
        }

        return $base;
    }
}