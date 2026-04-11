<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

/**
 * Controlador de Mesas
 *
 * Gestiona el CRUD de mesas del restaurante.
 * Las mesas pertenecen a una zona y tienen capacidad definida.
 *
 * Rutas asociadas:
 *   GET    /api/mesas        → index()   [público]
 *   POST   /api/mesas        → store()   [admin]
 *   GET    /api/mesas/{id}   → show()    [sin middleware definido]
 *   PUT    /api/mesas/{id}   → update()  [admin]
 *   DELETE /api/mesas/{id}   → destroy() [admin]
 */
class TableController extends Controller
{
    /**
     * Lista todas las mesas con su zona anidada.
     * Usado por el frontend para mostrar el mapa de mesas y el formulario de reservas.
     *
     * @return \Illuminate\Http\JsonResponse  Array de mesas con relación zone
     */
    public function index()
    {
        return response()->json(Table::with('zone')->get());
    }

    /**
     * Crea una nueva mesa.
     *
     * @param  Request $request  Campos requeridos: number, zone_id, capacity. Opcional: is_active
     * @return \Illuminate\Http\JsonResponse  201 con la mesa creada y su zona
     */
    public function store(Request $request)
    {
        $request->validate([
            'number'    => 'required|integer',
            'zone_id'   => 'required|exists:zones,id',
            'capacity'  => 'required|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $table = Table::create($request->all());

        return response()->json($table->load('zone'), 201);
    }

    /**
     * Devuelve una mesa específica con su zona.
     *
     * @param  int $id  ID de la mesa
     * @return \Illuminate\Http\JsonResponse  Mesa con zona | 404 si no existe
     */
    public function show($id)
    {
        return response()->json(Table::with('zone')->findOrFail($id));
    }

    /**
     * Actualiza los datos de una mesa existente.
     * Todos los campos son opcionales ('sometimes'), permitiendo actualizaciones parciales.
     *
     * @param  Request $request  Campos opcionales: number, zone_id, capacity, is_active
     * @param  int     $id       ID de la mesa
     * @return \Illuminate\Http\JsonResponse  Mesa actualizada con su zona
     */
    public function update(Request $request, $id)
    {
        $table = Table::findOrFail($id);

        $request->validate([
            'number'    => 'sometimes|integer',
            'zone_id'   => 'sometimes|exists:zones,id',
            'capacity'  => 'sometimes|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $table->update($request->all());

        return response()->json($table->load('zone'));
    }

    /**
     * Elimina una mesa permanentemente.
     * Solo accesible por administradores.
     *
     * @param  int $id  ID de la mesa
     * @return \Illuminate\Http\JsonResponse  204 sin contenido
     */
    public function destroy($id)
    {
        $table = Table::findOrFail($id);
        $table->delete();

        return response()->json(['message' => 'Mesa eliminada'], 204);
    }
}
