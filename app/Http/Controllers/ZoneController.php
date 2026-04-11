<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use Illuminate\Http\Request;

/**
 * Controlador de Zonas
 *
 * Gestiona el CRUD de zonas del restaurante (Salón Principal, Terraza, VIP, etc.).
 * Cada zona agrupa un conjunto de mesas.
 *
 * Rutas asociadas:
 *   GET    /api/zonas        → index()   [público]
 *   POST   /api/zonas        → store()   [admin]
 *   GET    /api/zonas/{id}   → show()    [sin middleware definido]
 *   PUT    /api/zonas/{id}   → update()  [admin]
 *   DELETE /api/zonas/{id}   → destroy() [admin]
 */
class ZoneController extends Controller
{
    /**
     * Lista todas las zonas disponibles.
     * Usado por el frontend para poblar el selector de zona en el formulario de reservas.
     *
     * @return \Illuminate\Http\JsonResponse  Array de zonas { id, name, icon }
     */
    public function index()
    {
        return response()->json(Zone::all());
    }

    /**
     * Crea una nueva zona.
     *
     * @param  Request $request  Campo requerido: name. Opcional: icon (emoji)
     * @return \Illuminate\Http\JsonResponse  201 con la zona creada
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string' // emoji representativo de la zona
        ]);

        $zone = Zone::create($request->all());

        return response()->json($zone, 201);
    }

    /**
     * Devuelve una zona específica.
     *
     * @param  int $id  ID de la zona
     * @return \Illuminate\Http\JsonResponse  Zona | 404 si no existe
     */
    public function show($id)
    {
        return response()->json(Zone::findOrFail($id));
    }

    /**
     * Actualiza los datos de una zona.
     * Campos opcionales ('sometimes') para permitir actualizaciones parciales.
     *
     * @param  Request $request  Campos opcionales: name, icon
     * @param  int     $id       ID de la zona
     * @return \Illuminate\Http\JsonResponse  Zona actualizada
     */
    public function update(Request $request, $id)
    {
        $zone = Zone::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'icon' => 'nullable|string'
        ]);

        $zone->update($request->all());

        return response()->json($zone);
    }

    /**
     * Elimina una zona permanentemente.
     * Solo accesible por administradores.
     *
     * @param  int $id  ID de la zona
     * @return \Illuminate\Http\JsonResponse  204 sin contenido
     */
    public function destroy($id)
    {
        $zone = Zone::findOrFail($id);
        $zone->delete();

        return response()->json(['message' => 'Zona eliminada'], 204);
    }
}
