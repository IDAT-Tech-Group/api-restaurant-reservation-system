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
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Public GET endpoints
Route::get('/zonas', [ZoneController::class, 'index']);
Route::get('/mesas', [TableController::class, 'index']);
Route::get('/horarios', [TimeSlotController::class, 'index']);
Route::get('/platos', [DishController::class, 'index']);

// Public Reservation functionality (Client or public)
// For create, usually users should be logged in, but requirement says "Cualquier cliente logueado o admin" or public fallback.
Route::post('/reservas', [ReservationController::class, 'store']);
Route::patch('/reservas/{id}/status', [ReservationController::class, 'updateStatus']); // Simular pago 50%

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin protected routes
    Route::middleware([\App\Http\Middleware\AdminMiddleware::class])->group(function () {
        // Zones DB Maintenance
        Route::post('/zonas', [ZoneController::class, 'store']);
        Route::put('/zonas/{id}', [ZoneController::class, 'update']);
        Route::delete('/zonas/{id}', [ZoneController::class, 'destroy']);

        // Tables DB Maintenance
        Route::post('/mesas', [TableController::class, 'store']);
        Route::put('/mesas/{id}', [TableController::class, 'update']);
        Route::delete('/mesas/{id}', [TableController::class, 'destroy']);

        // TimeSlots DB Maintenance
        Route::post('/horarios', [TimeSlotController::class, 'store']);
        Route::put('/horarios/{id}', [TimeSlotController::class, 'update']);
        Route::delete('/horarios/{id}', [TimeSlotController::class, 'destroy']);

        // Dishes DB Maintenance
        Route::post('/platos', [DishController::class, 'store']);
        Route::put('/platos/{id}', [DishController::class, 'update']);
        Route::delete('/platos/{id}', [DishController::class, 'destroy']);

        // Admin Reservation Management
        Route::get('/reservas', [ReservationController::class, 'index']);
        Route::delete('/reservas/{id}', [ReservationController::class, 'destroy']);

        // Admin Customers Management
        Route::get('/clientes', [CustomerController::class, 'index']);
        Route::put('/clientes/{id}/perfil', [CustomerController::class, 'updateProfile']);
        Route::patch('/clientes/{id}/perfil', [CustomerController::class, 'updateProfile']);
    });
});
