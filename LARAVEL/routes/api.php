<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::get('/events', [EventController::class, 'index']);
// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    // Autenticación
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Usuarios (solo gerentes)
    Route::middleware('role:3')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::put('/users/{user}/toggle-status', [UserController::class, 'toggleStatus']);
    });

    // Servicios
    Route::get('/services', [ServiceController::class, 'index']);
    Route::middleware('role:3')->group(function () {
        Route::post('/services', [ServiceController::class, 'store']);
        Route::put('/services/{service}', [ServiceController::class, 'update']);
        Route::delete('/services/{service}', [ServiceController::class, 'destroy']);
        Route::put('/services/{service}/toggle-status', [ServiceController::class, 'toggleStatus']);
    });

    // Eventos
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{event}', [EventController::class, 'show']);
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{event}', [EventController::class, 'update']);
    Route::put('/events/{event}/assign-employee', [EventController::class, 'assignEmployee']);
    Route::put('/events/{event}/change-status', [EventController::class, 'changeStatus']);
    Route::post('/events/{event}/seguimientos', [EventController::class, 'addFollowUp']);
});
