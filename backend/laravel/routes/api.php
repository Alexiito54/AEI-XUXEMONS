<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\XuxemonController;
use App\Http\Controllers\ColeccionController;
use App\Http\Controllers\CombateController; 
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MochilaController;

// Rutas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// 👇 Xuxemons públicos — sin auth
Route::get('/xuxemons',      [XuxemonController::class, 'index']);
Route::get('/xuxemons/{id}', [XuxemonController::class, 'show']);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Colección
    Route::get('/coleccion',         [ColeccionController::class, 'index']);
    Route::post('/coleccion',        [ColeccionController::class, 'store']);
    Route::delete('/coleccion/{id}', [ColeccionController::class, 'destroy']);

    // Combates — cualquier entrenador autenticado
    Route::get('/combates',      [CombateController::class, 'index']);
    Route::get('/combates/{id}', [CombateController::class, 'show']);
    Route::post('/combates',     [CombateController::class, 'store']);

    // Solo admin
    Route::middleware('role:admin')->group(function () {
        Route::post('/xuxemons',        [XuxemonController::class, 'store']);
        Route::put('/xuxemons/{id}',    [XuxemonController::class, 'update']);
        Route::delete('/xuxemons/{id}', [XuxemonController::class, 'destroy']);

        Route::get('/users',         [UserController::class, 'index']);
        Route::get('/users/{id}',    [UserController::class, 'show']);
        Route::put('/users/{id}',    [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
    });

    Route::middleware('auth:sanctum')->group(function () {
    Route::get('/mochila',        [MochilaController::class, 'index']);
    Route::post('/mochila',       [MochilaController::class, 'store']);
    Route::delete('/mochila/{id}',[MochilaController::class, 'destroy']);
});
});
