<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use Illuminate\Http\Request;

/**
 * Controlador de Platos (Menú / Carta)
 *
 * Gestiona el CRUD de platos del restaurante.
 * El listado es público; crear, editar y eliminar requieren rol admin.
 *
 * Rutas asociadas:
 *   GET    /api/platos        → index()   [público]
 *   POST   /api/platos        → store()   [admin]
 *   GET    /api/platos/{id}   → show()    [sin middleware definido]
 *   PUT    /api/platos/{id}   → update()  [admin]
 *   DELETE /api/platos/{id}   → destroy() [admin]
 */
class DishController extends Controller
{
    /**
     * Lista todos los platos del menú.
     *
     * @return \Illuminate\Http\JsonResponse  Array de platos
     */
    public function index()
    {
        return response()->json(Dish::all());
    }

    /**
     * Crea un nuevo plato.
     *
     * @param  Request $request  Campos requeridos: name, price.
     *                           Opcionales: description, emoji, category, status
     * @return \Illuminate\Http\JsonResponse  201 con el plato creado
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'emoji'       => 'nullable|string',    // emoji decorativo para la UI
            'category'    => 'nullable|string',    // ej: 'Entradas', 'Fondos', 'Postres'
            'status'      => 'boolean'             // true = disponible, false = no disponible
        ]);

        $dish = Dish::create($request->all());

        return response()->json($dish, 201);
    }

    /**
     * Devuelve un plato específico.
     *
     * @param  int $id  ID del plato
     * @return \Illuminate\Http\JsonResponse  Plato | 404 si no existe
     */
    public function show($id)
    {
        return response()->json(Dish::findOrFail($id));
    }

    /**
     * Actualiza los datos de un plato.
     * Campos opcionales ('sometimes') para permitir actualizaciones parciales.
     *
     * @param  Request $request  Campos opcionales: name, description, price, emoji, category, status
     * @param  int     $id       ID del plato
     * @return \Illuminate\Http\JsonResponse  Plato actualizado
     */
    public function update(Request $request, $id)
    {
        $dish = Dish::findOrFail($id);

        $request->validate([
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'sometimes|numeric|min:0',
            'emoji'       => 'nullable|string',
            'category'    => 'nullable|string',
            'status'      => 'boolean'
        ]);

        $dish->update($request->all());

        return response()->json($dish);
    }

    /**
     * Elimina un plato permanentemente.
     * Solo accesible por administradores.
     *
     * @param  int $id  ID del plato
     * @return \Illuminate\Http\JsonResponse  204 sin contenido
     */
    public function destroy($id)
    {
        $dish = Dish::findOrFail($id);
        $dish->delete();

        return response()->json(['message' => 'Plato eliminado'], 204);
    }
}
