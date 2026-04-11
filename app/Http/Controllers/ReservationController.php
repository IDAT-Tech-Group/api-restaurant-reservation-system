<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Controlador de Reservas
 *
 * Gestiona el ciclo completo de una reserva: listar, crear, ver, actualizar estado y eliminar.
 * Incluye lógica de control de acceso (admin vs cliente) y validación de solapamiento de horarios.
 *
 * Rutas asociadas:
 *   GET    /api/reservas              → index()         [auth:sanctum]
 *   POST   /api/reservas              → store()         [auth:sanctum]
 *   GET    /api/reservas/{id}         → show()          [sin middleware definido]
 *   PATCH  /api/reservas/{id}/status  → updateStatus()  [público]
 *   DELETE /api/reservas/{id}         → destroy()       [auth:sanctum + AdminMiddleware]
 */
class ReservationController extends Controller
{
    /**
     * Lista reservas según el rol del usuario autenticado.
     * - Admin: ve todas las reservas (con filtros opcionales por fecha, estado o user_id).
     * - Cliente: ve únicamente sus propias reservas (filtradas por user_id automáticamente).
     *
     * @param  Request $request  Parámetros opcionales: date, status, user_id (solo admin)
     * @return \Illuminate\Http\JsonResponse  Array de reservas con relaciones user y table.zone
     */
    public function index(Request $request)
    {
        // Cargar relaciones: usuario dueño de la reserva y mesa con su zona
        $query = Reservation::with(['user', 'table.zone']);

        $authUser = $request->user();

        // Los clientes solo pueden ver sus propias reservas
        if ($authUser && $authUser->role !== 'admin') {
            $query->where('user_id', $authUser->id);
        }

        // Filtros opcionales de consulta
        if ($request->has('date')) {
            $query->where('date', $request->date);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // El admin puede filtrar por user_id específico para ver reservas de un cliente
        if ($request->has('user_id') && $authUser && $authUser->role === 'admin') {
            $query->where('user_id', $request->user_id);
        }

        return response()->json($query->get());
    }

    /**
     * Crea una nueva reserva para el usuario autenticado.
     * Valida que la mesa tenga capacidad suficiente y que no exista solapamiento
     * de horario con otra reserva activa en la misma mesa y fecha.
     *
     * @param  Request $request  Campos requeridos: table_id, date, start_time, end_time, persons
     *                           Opcionales: tipopago, notes
     * @return \Illuminate\Http\JsonResponse  201 con la reserva creada | 422 si hay conflicto
     */
    public function store(Request $request)
    {
        $request->validate([
            'table_id'   => 'required|exists:tables,id',
            'date'       => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'persons'    => 'required|integer|min:1',
            'tipopago'   => 'nullable|string',
            'notes'      => 'nullable|string'
        ]);

        // Verificar que el número de personas no supere la capacidad de la mesa
        $table = Table::findOrFail($request->table_id);
        if ($request->persons > $table->capacity) {
            return response()->json(['message' => 'La cantidad de personas supera la capacidad de la mesa'], 422);
        }

        // Verificar solapamiento de horarios en la misma mesa y fecha
        // Se consideran 3 casos de conflicto:
        $conflict = Reservation::where('table_id', $request->table_id)
            ->where('date', $request->date)
            ->where(function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    // Caso 1: la nueva reserva empieza dentro de una existente
                    $q->where('start_time', '<=', $request->start_time)
                      ->where('end_time', '>', $request->start_time);
                })->orWhere(function ($q) use ($request) {
                    // Caso 2: la nueva reserva termina dentro de una existente
                    $q->where('start_time', '<', $request->end_time)
                      ->where('end_time', '>=', $request->end_time);
                })->orWhere(function ($q) use ($request) {
                    // Caso 3: la nueva reserva envuelve completamente a una existente
                    $q->where('start_time', '>=', $request->start_time)
                      ->where('end_time', '<=', $request->end_time);
                });
            })
            ->where('status', '!=', 'cancelado') // las canceladas no bloquean el horario
            ->exists();

        if ($conflict) {
            return response()->json(['message' => 'La mesa no está disponible en este horario'], 422);
        }

        $data = $request->all();
        // Asociar la reserva al usuario autenticado (si hay token, siempre lo habrá por auth:sanctum)
        if ($request->user()) {
            $data['user_id'] = $request->user()->id;
        }

        $res = Reservation::create($data);

        // Devolver la reserva con las relaciones cargadas para que el frontend no necesite otro fetch
        return response()->json($res->load(['user', 'table.zone']), 201);
    }

    /**
     * Devuelve una reserva específica con sus relaciones.
     *
     * @param  int $id  ID de la reserva
     * @return \Illuminate\Http\JsonResponse  Reserva con user y table.zone | 404 si no existe
     */
    public function show($id)
    {
        return response()->json(Reservation::with(['user', 'table.zone'])->findOrFail($id));
    }

    /**
     * Actualiza el estado de una reserva.
     * Ruta pública para permitir al cliente simular el flujo de pago sin autenticación.
     * Estados válidos: pendiente → reservado → completado → cancelado
     *
     * @param  Request $request  Campo requerido: status
     * @param  int     $id       ID de la reserva
     * @return \Illuminate\Http\JsonResponse  Reserva actualizada con relaciones
     */
    public function updateStatus(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        $request->validate([
            'status' => 'required|string|in:pendiente,reservado,completado,cancelado'
        ]);

        $reservation->update(['status' => $request->status]);

        return response()->json($reservation->load(['user', 'table.zone']));
    }

    /**
     * Elimina permanentemente una reserva.
     * Solo accesible por administradores (AdminMiddleware).
     *
     * @param  int $id  ID de la reserva a eliminar
     * @return \Illuminate\Http\JsonResponse  204 sin contenido
     */
    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();

        return response()->json(['message' => 'Reserva eliminada'], 204);
    }
}
