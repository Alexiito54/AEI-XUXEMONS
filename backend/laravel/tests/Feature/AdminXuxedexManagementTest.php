<?php

namespace Tests\Feature;

use App\Models\Coleccion;
use App\Models\User;
use App\Models\Xuxemon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminXuxedexManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_registered_players(): void
    {
        $admin = $this->crearUsuario('Admin', 'Principal', 'admin@example.com');
        $jugador = $this->crearUsuario('Luna', 'Player', 'luna@example.com');
        $otroJugador = $this->crearUsuario('Sol', 'Player', 'sol@example.com');

        $xuxemon = Xuxemon::create([
            'nombre' => 'Slime',
            'tipo' => 'Agua',
            'tamaño' => 'Pequeño',
            'imagen' => 'slime.png',
        ]);
        $segundoXuxemon = Xuxemon::create([
            'nombre' => 'Golem',
            'tipo' => 'Tierra',
            'tamaño' => 'Mediano',
            'imagen' => 'golem.png',
        ]);

        Coleccion::create([
            'id_usuario' => $jugador->id,
            'id_xuxemon' => $xuxemon->id,
        ]);
        Coleccion::create([
            'id_usuario' => $jugador->id,
            'id_xuxemon' => $xuxemon->id,
        ]);
        Coleccion::create([
            'id_usuario' => $jugador->id,
            'id_xuxemon' => $segundoXuxemon->id,
        ]);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/admin/jugadores');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'jugadores')
            ->assertJsonFragment([
                'id' => $jugador->id,
                'id_usuario' => $jugador->id_usuario,
                'total_xuxemons' => 2,
            ])
            ->assertJsonFragment([
                'id' => $otroJugador->id,
                'id_usuario' => $otroJugador->id_usuario,
                'total_xuxemons' => 0,
            ]);
    }

    public function test_admin_can_assign_random_xuxemon_to_a_player(): void
    {
        $admin = $this->crearUsuario('Admin', 'Principal', 'admin@example.com');
        $jugador = $this->crearUsuario('Luna', 'Player', 'luna@example.com');

        $xuxemon = Xuxemon::create([
            'nombre' => 'Dragon Agua',
            'tipo' => 'Agua',
            'tamaño' => 'Mediano',
            'imagen' => 'dragon-agua.png',
        ]);

        Sanctum::actingAs($admin);

        $response = $this->postJson("/api/admin/jugadores/{$jugador->id}/xuxemon-aleatorio");

        $response
            ->assertCreated()
            ->assertJsonFragment([
                'id' => $jugador->id,
                'id_usuario' => $jugador->id_usuario,
                'total_xuxemons' => 1,
            ])
            ->assertJsonFragment([
                'id' => $xuxemon->id,
                'nombre' => 'Dragon Agua',
            ]);

        $this->assertDatabaseHas('colecciones', [
            'id_usuario' => $jugador->id,
            'id_xuxemon' => $xuxemon->id,
        ]);
    }

    public function test_non_admin_cannot_access_admin_xuxedex_management_routes(): void
    {
        $admin = $this->crearUsuario('Admin', 'Principal', 'admin@example.com');
        $jugador = $this->crearUsuario('Luna', 'Player', 'luna@example.com');

        Sanctum::actingAs($jugador);

        $listResponse = $this->getJson('/api/admin/jugadores');
        $assignResponse = $this->postJson("/api/admin/jugadores/{$admin->id}/xuxemon-aleatorio");

        $listResponse->assertForbidden();
        $assignResponse->assertForbidden();
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
