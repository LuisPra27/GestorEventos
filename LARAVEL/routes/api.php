<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// Rutas publicas
Route::get('/health', function () {
    $response = [
        'status' => 'ok',
        'timestamp' => now()->toDateTimeString(),
        'app' => config('app.name', 'Gestor de Eventos'),
        'env' => config('app.env', 'unknown')
    ];

    try {
        // Intentar conexión a la base de datos
        DB::connection()->getPdo();
        $response['database'] = 'connected';
        $response['db_host'] = config('database.connections.pgsql.host');

        // Verificar que las tablas existen
        $tableCount = DB::select("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = 'public'");
        $response['tables'] = $tableCount[0]->count ?? 0;

    } catch (\Exception $e) {
        $response['status'] = 'warning';
        $response['database'] = 'disconnected';
        $response['error'] = $e->getMessage();

        // Si la base de datos no está disponible, devolver 200 pero con warning
        // para que Railway no falle el health check inmediatamente
        return response()->json($response, 200);
    }

    return response()->json($response);
});

// Health check simple para Railway (sin base de datos)
Route::get('/ping', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'pong',
        'timestamp' => now()->toDateTimeString()
    ]);
});

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::get('/events', [EventController::class, 'index']);
// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    // Autenticacion
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Usuarios (solo gerentes)
    Route::middleware('role:3')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::put('/users/{user}/toggle-status', [UserController::class, 'toggleStatus']);
    });

    // Servicios
    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/services/active', [ServiceController::class, 'indexActive']); // Solo servicios activos para clientes
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
