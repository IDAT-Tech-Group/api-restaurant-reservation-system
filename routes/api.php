<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\TimeSlotController;
use App\Http\Controllers\DishController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReservationController;

/*
|--------------------------------------------------------------------------
| Rutas del API REST - Restaurante
|--------------------------------------------------------------------------
| Estructura de acceso:
|   - Públicas:       sin middleware (login, register, consultas de catálogo)
|   - Autenticadas:   middleware auth:sanctum (token Bearer requerido)
|   - Solo admin:     auth:sanctum + AdminMiddleware (role = 'admin')
|
| Todas las rutas tienen el prefijo /api/ (definido en RouteServiceProvider).
*/

// ---------------------------------------------------------------
// AUTENTICACIÓN (públicas, sin token)
// ---------------------------------------------------------------
Route::post('/login',    [AuthController::class, 'login']);    // Iniciar sesión → devuelve token + user
Route::post('/register', [AuthController::class, 'register']); // Registrar cuenta → devuelve token + user

// ---------------------------------------------------------------
// CONSULTAS PÚBLICAS DE CATÁLOGO (solo lectura, sin autenticación)
// Permiten al frontend mostrar datos antes de que el usuario inicie sesión
// ---------------------------------------------------------------
Route::get('/zonas',    [ZoneController::class,     'index']); // Listar zonas del restaurante
Route::get('/mesas',    [TableController::class,    'index']); // Listar mesas con su zona anidada
Route::get('/horarios', [TimeSlotController::class, 'index']); // Listar turnos horarios disponibles
Route::get('/platos',   [DishController::class,     'index']); // Listar menú / carta de platos

// ---------------------------------------------------------------
// RESERVAS - Acción pública especial
// Permite actualizar el estado sin token (simular flujo de pago del cliente)
// ---------------------------------------------------------------
Route::patch('/reservas/{id}/status', [ReservationController::class, 'updateStatus']); // Actualizar estado (simular pago 50%)

// ---------------------------------------------------------------
// RUTAS PROTEGIDAS - Requieren token Bearer (auth:sanctum)
// El token se obtiene al hacer login o register
// ---------------------------------------------------------------
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']); // Invalida el token actual en el servidor

    // Reservas del usuario autenticado
    // El controlador filtra automáticamente por user_id si no es admin
    Route::post('/reservas', [ReservationController::class, 'store']); // Crear reserva (asocia user_id automáticamente)
    Route::get('/reservas',  [ReservationController::class, 'index']); // Listar reservas propias (o todas si es admin)

    // ---------------------------------------------------------------
    // RUTAS SOLO ADMIN - Requieren role = 'admin' (AdminMiddleware)
    // ---------------------------------------------------------------
    Route::middleware([\App\Http\Middleware\AdminMiddleware::class])->group(function () {

        // Mantenimiento de Zonas
        Route::post('/zonas',        [ZoneController::class, 'store']);   // Crear zona
        Route::put('/zonas/{id}',    [ZoneController::class, 'update']);  // Editar zona
        Route::delete('/zonas/{id}', [ZoneController::class, 'destroy']); // Eliminar zona

        // Mantenimiento de Mesas
        Route::post('/mesas',        [TableController::class, 'store']);   // Crear mesa
        Route::put('/mesas/{id}',    [TableController::class, 'update']);  // Editar mesa (zona, capacidad, número)
        Route::delete('/mesas/{id}', [TableController::class, 'destroy']); // Eliminar mesa

        // Mantenimiento de Turnos Horarios
        Route::post('/horarios',        [TimeSlotController::class, 'store']);   // Crear turno
        Route::put('/horarios/{id}',    [TimeSlotController::class, 'update']);  // Editar turno
        Route::delete('/horarios/{id}', [TimeSlotController::class, 'destroy']); // Eliminar turno

        // Mantenimiento del Menú / Platos
        Route::post('/platos',        [DishController::class, 'store']);   // Crear plato
        Route::put('/platos/{id}',    [DishController::class, 'update']);  // Editar plato
        Route::delete('/platos/{id}', [DishController::class, 'destroy']); // Eliminar plato

        // Gestión de Reservas (admin)
        Route::delete('/reservas/{id}', [ReservationController::class, 'destroy']); // Eliminar cualquier reserva

        // Gestión de Clientes (admin)
        Route::get('/clientes',              [CustomerController::class, 'index']);         // Listar todos los clientes
        Route::put('/clientes/{id}/perfil',  [CustomerController::class, 'updateProfile']); // Editar perfil completo
        Route::patch('/clientes/{id}/perfil',[CustomerController::class, 'updateProfile']); // Editar perfil parcial
    });
});
