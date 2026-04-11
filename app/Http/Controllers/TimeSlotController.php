<?php

namespace App\Http\Controllers;

use App\Models\TimeSlot;
use Illuminate\Http\Request;

/**
 * Controlador de Turnos Horarios
 *
 * Gestiona el CRUD de los turnos de atención del restaurante.
 * Cada turno define una hora de inicio y duración en minutos,
 * usados como referencia para el formulario de reservas.
 *
 * Rutas asociadas:
 *   GET    /api/horarios        → index()   [público]
 *   POST   /api/horarios        → store()   [admin]
 *   GET    /api/horarios/{id}   → show()    [sin middleware definido]
 *   PUT    /api/horarios/{id}   → update()  [admin]
 *   DELETE /api/horarios/{id}   → destroy() [admin]
 */
class TimeSlotController extends Controller
{
    /**
     * Lista todos los turnos horarios disponibles.
     * Usado por el frontend para poblar el selector de hora en el formulario de reservas.
     *
     * @return \Illuminate\Http\JsonResponse  Array de turnos { id, start_time, duration }
     */
    public function index()
    {
        return response()->json(TimeSlot::all());
    }

    /**
     * Crea un nuevo turno horario.
     *
     * @param  Request $request  Campos requeridos: start_time (HH:MM), duration (minutos)
     * @return \Illuminate\Http\JsonResponse  201 con el turno creado
     */
    public function store(Request $request)
    {
        $request->validate([
            'start_time' => 'required|date_format:H:i', // formato 24h, ej: "13:00"
            'duration'   => 'required|integer|min:1'    // duración en minutos
        ]);

        $slot = TimeSlot::create($request->all());

        return response()->json($slot, 201);
    }

    /**
     * Devuelve un turno horario específico.
     *
     * @param  int $id  ID del turno
     * @return \Illuminate\Http\JsonResponse  Turno | 404 si no existe
     */
    public function show($id)
    {
        return response()->json(TimeSlot::findOrFail($id));
    }

    /**
     * Actualiza un turno horario existente.
     * Campos opcionales ('sometimes') para permitir actualizaciones parciales.
     *
     * @param  Request $request  Campos opcionales: start_time, duration
     * @param  int     $id       ID del turno
     * @return \Illuminate\Http\JsonResponse  Turno actualizado
     */
    public function update(Request $request, $id)
    {
        $slot = TimeSlot::findOrFail($id);

        $request->validate([
            'start_time' => 'sometimes|date_format:H:i',
            'duration'   => 'sometimes|integer|min:1'
        ]);

        $slot->update($request->all());

        return response()->json($slot);
    }

    /**
     * Elimina un turno horario permanentemente.
     * Solo accesible por administradores.
     *
     * @param  int $id  ID del turno
     * @return \Illuminate\Http\JsonResponse  204 sin contenido
     */
    public function destroy($id)
    {
        $slot = TimeSlot::findOrFail($id);
        $slot->delete();

        return response()->json(['message' => 'Turno eliminado'], 204);
    }
}
