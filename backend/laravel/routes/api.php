<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MochilaController;
use App\Http\Controllers\XuxemonController;
use App\Http\Controllers\ColeccionController;
use App\Http\Controllers\EnfermedadController;
use App\Http\Controllers\VacunaController;
use App\Http\Controllers\ConfiguracionAdminController;
use App\Http\Controllers\DiarioController;

Route::options('/{any}', function () {
    return response('', 200);
})->where('any', '.*');

// Public routes (no authentication required)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public API endpoints
Route::get('/items', [ItemController::class, 'index']);
Route::get('/items/{item}', [ItemController::class, 'show']);
Route::get('/xuxemons', [XuxemonController::class, 'index']);
Route::get('/xuxemons/{xuxemon}', [XuxemonController::class, 'show']);
Route::get('/enfermedades', [EnfermedadController::class, 'index']);
Route::get('/enfermedades/{enfermedad}', [EnfermedadController::class, 'show']);
Route::get('/vacunas', [VacunaController::class, 'index']);
Route::get('/vacunas/{vacuna}', [VacunaController::class, 'show']);

// Protected routes (requires authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/perfil/stats', [UserController::class, 'perfilStats']);

    // User routes
    Route::get('/user', [UserController::class, 'getUser']);
    Route::put('/user', [UserController::class, 'updateUser']);
    Route::put('/user/password', [UserController::class, 'changePassword']);
    Route::delete('/user', [UserController::class, 'deleteUser']);

    // Mochila routes
    Route::get('/mochila', [MochilaController::class, 'index']);
    Route::post('/mochila', [MochilaController::class, 'store']);
    Route::delete('/mochila/{mochila}', [MochilaController::class, 'destroy']);

    // Coleccion routes
    Route::get('/colecciones', [ColeccionController::class, 'index']);
    Route::get('/xuxedex', [ColeccionController::class, 'xuxedex']);
    Route::post('/colecciones', [ColeccionController::class, 'store']);
    Route::post('/colecciones/{coleccion}/alimentar', [ColeccionController::class, 'alimentar']);
    Route::post('/colecciones/{coleccion}/curar', [ColeccionController::class, 'curar']);
    Route::delete('/colecciones/{coleccion}', [ColeccionController::class, 'destroy']);

    // Diario routes
    Route::post('/diario/xuxes', [DiarioController::class, 'reclamarXuxesDiarios']);
    Route::post('/diario/xuxemon', [DiarioController::class, 'reclamarXuxemonDiario']);

    // Admin only routes
    Route::middleware('admin')->group(function () {
        Route::get('/admin/jugadores', [UserController::class, 'indexJugadores']);
        Route::post('/admin/jugadores/{user}/xuxemon-aleatorio', [ColeccionController::class, 'storeForUser']);
        Route::post('/items', [ItemController::class, 'store']);
        Route::post('/xuxemons', [XuxemonController::class, 'store']);
        Route::post('/enfermedades', [EnfermedadController::class, 'store']);
        Route::post('/vacunas', [VacunaController::class, 'store']);
        Route::get('/configuracion', [ConfiguracionAdminController::class, 'show']);
        Route::put('/configuracion', [ConfiguracionAdminController::class, 'update']);
        Route::get('/admin/jugadores/{user}/coleccion', [ColeccionController::class, 'indexForUser']);
    });
});
