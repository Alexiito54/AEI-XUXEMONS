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

    /**
     * Obtener el usuario propietario de este Xuxemon en su colección.
     * N Colecciones -> 1 Usuario (muchos Xuxemons de 1 usuario)
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    /**
     * Obtener la definición (plantilla) del Xuxemon en esta colección.
     * N Colecciones -> 1 Xuxemon (muchas instancias del mismo Xuxemon)
     */
    public function xuxemon(): BelongsTo
    {
        return $this->belongsTo(Xuxemon::class, 'id_xuxemon');
    }

    /**
     * Obtener todas las enfermedades que afectan a este Xuxemon específico.
     * 1 Xuxemon (Coleccion) -> N Enfermedades
     */
    public function enfermedades(): BelongsToMany
    {
        return $this->belongsToMany(Enfermedad::class, 'xuxemon_enfermedad', 'coleccion_id', 'enfermedad_id')
                    ->withTimestamps()
                    ->withPivot('fecha_contagio');
    }

    /**
     * Verificar si el Xuxemon está enfermo (tiene al menos una enfermedad).
     */
    public function estaEnfermo(): bool
    {
        return $this->enfermedades()->exists();
    }

    /**
     * Verificar si el Xuxemon tiene una enfermedad específica.
     */
    public function tieneEnfermedad(string $nombreEnfermedad): bool
    {
        return $this->enfermedades()
                    ->where('nombre', $nombreEnfermedad)
                    ->exists();
    }

    /**
     * Verificar si el Xuxemon puede alimentarse (no tiene "Atracón").
     */
    public function puedAlimentarse(): bool
    {
        return !$this->tieneEnfermedad('Atracón');
    }

    /**
     * Obtener el número de xuxes necesarios para crecer considerando enfermedades.
     */
    public function xuxesNecesariosParaCrecer(): int
    {
        $base = match ($this->tamaño_actual) {
            'Pequeño' => 3,
            'Mediano' => 5,
            default => 999, // No puede crecer desde Grande
        };

        // Si tiene "Bajón de azúcar", requiere 2 xuxes extra
        if ($this->tieneEnfermedad('Bajón de azúcar')) {
            $base += 2;
        }

        return $base;
    }
}


