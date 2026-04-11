<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

/**
 * Controlador de Clientes
 *
 * Gestión de usuarios con rol 'client' desde el panel de administración.
 * Solo accesible por administradores (AdminMiddleware).
 *
 * Rutas asociadas:
 *   GET   /api/clientes              → index()         [admin]
 *   PUT   /api/clientes/{id}/perfil  → updateProfile() [admin]
 *   PATCH /api/clientes/{id}/perfil  → updateProfile() [admin]
 */
class CustomerController extends Controller
{
    /**
     * Lista todos los usuarios con rol 'client'.
     * Excluye administradores del listado.
     *
     * @return \Illuminate\Http\JsonResponse  Array de usuarios clientes
     */
    public function index()
    {
        // Filtrar solo usuarios con rol 'client', excluyendo admins
        $clientes = User::where('role', 'client')->get();
        return response()->json($clientes);
    }

    /**
     * Actualiza el perfil de un cliente específico.
     * Solo permite modificar nombre y teléfono (no email ni contraseña desde aquí).
     * Usa $request->only() para evitar mass assignment no deseado.
     *
     * @param  Request $request  Campos opcionales: name, phone
     * @param  int     $id       ID del usuario cliente
     * @return \Illuminate\Http\JsonResponse  Usuario actualizado | 404 si no existe o no es cliente
     */
    public function updateProfile(Request $request, $id)
    {
        // Buscar solo entre clientes (no permite editar admins por esta ruta)
        $user = User::where('role', 'client')->findOrFail($id);

        $request->validate([
            'name'  => 'sometimes|string|max:255',
            'phone' => 'nullable|string'
        ]);

        // Actualizar solo name y phone, ignorando cualquier otro campo enviado
        $user->update($request->only('name', 'phone'));

        return response()->json($user);
    }
}
