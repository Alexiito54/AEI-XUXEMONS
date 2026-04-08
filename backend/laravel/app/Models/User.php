<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Events\Creating;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'apellidos',
        'email',
        'password',
        'id_usuario',
        'rol',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // Generar id_usuario con formato #NombreXXXX
            $user->id_usuario = static::generarIdUsuario($user->name);

            // Asignar rol administrador si es el primer usuario
            if (static::count() === 0) {
                $user->rol = 'administrador';
            } else {
                $user->rol = 'jugador';
            }
        });
    }

    /**
     * Generar un id_usuario único con formato #NombreXXXX donde XXXX es un número aleatorio de 4 dígitos.
     *
     * @param string $nombre
     * @return string
     */
    public static function generarIdUsuario($nombre)
    {
        $nombreLimpio = str_replace(' ', '', $nombre);
        $codigoAleatorio = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        return '#' . $nombreLimpio . $codigoAleatorio;
    }

    public function mochilas(): HasMany
    {
        return $this->hasMany(Mochila::class);
    }

    public function colecciones(): HasMany
    {
        return $this->hasMany(Coleccion::class);
    }
}
