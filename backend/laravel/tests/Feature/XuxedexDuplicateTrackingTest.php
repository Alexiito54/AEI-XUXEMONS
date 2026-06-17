<?php

namespace Tests\Feature;

use App\Models\Coleccion;
use App\Models\User;
use App\Models\Xuxemon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class XuxedexDuplicateTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_xuxedex_reports_duplicate_captures_without_inflating_unique_counts(): void
    {
        $this->crearUsuario('Admin', 'Principal', 'admin@example.com');
        $jugador = $this->crearUsuario('Ana', 'Player', 'ana@example.com');

        $slime = Xuxemon::create([
            'nombre' => 'Slime Agua',
            'tipo' => 'Agua',
            'tamaño' => 'Pequeño',
            'imagen' => 'slime-agua.png',
        ]);
        $golem = Xuxemon::create([
            'nombre' => 'Golem Tierra',
            'tipo' => 'Tierra',
            'tamaño' => 'Mediano',
            'imagen' => 'golem-tierra.png',
        ]);
        Xuxemon::create([
            'nombre' => 'Cabra Aire',
            'tipo' => 'Aire',
            'tamaño' => 'Grande',
            'imagen' => 'cabra-aire.png',
        ]);

        Coleccion::create([
            'id_usuario' => $jugador->id,
            'id_xuxemon' => $slime->id,
        ]);
        Coleccion::create([
            'id_usuario' => $jugador->id,
            'id_xuxemon' => $slime->id,
        ]);
        Coleccion::create([
            'id_usuario' => $jugador->id,
            'id_xuxemon' => $golem->id,
        ]);

        Sanctum::actingAs($jugador);

        $response = $this->getJson('/api/xuxedex');

        $response
            ->assertOk()
            ->assertJsonPath('estadisticas.total', 3)
            ->assertJsonPath('estadisticas.atrapados', 2)
            ->assertJsonFragment([
                'id' => $slime->id,
                'cantidad_capturada' => 2,
                'atrapado' => true,
            ])
            ->assertJsonFragment([
                'id' => $golem->id,
                'cantidad_capturada' => 1,
                'atrapado' => true,
            ]);
    }

    private function crearUsuario(string $name, string $apellidos, string $email): User
    {
        return User::create([
            'name' => $name,
            'apellidos' => $apellidos,
            'email' => $email,
            'password' => Hash::make('password'),
        ]);
    }
}
