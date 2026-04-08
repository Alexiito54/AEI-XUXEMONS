<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MochilaController;
use App\Http\Controllers\XuxemonController;
use App\Http\Controllers\ColeccionController;

// Public routes (no authentication required)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public API endpoints
Route::get('/items', [ItemController::class, 'index']);
Route::get('/items/{item}', [ItemController::class, 'show']);
Route::get('/xuxemons', [XuxemonController::class, 'index']);
Route::get('/xuxemons/{xuxemon}', [XuxemonController::class, 'show']);

// Protected routes (requires authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);

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
    Route::post('/colecciones', [ColeccionController::class, 'store']);
    Route::delete('/colecciones/{coleccion}', [ColeccionController::class, 'destroy']);

    // Admin only routes
    Route::middleware('admin')->group(function () {
        Route::post('/items', [ItemController::class, 'store']);
        Route::post('/xuxemons', [XuxemonController::class, 'store']);
    });
});

