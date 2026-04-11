<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Controlador de Autenticación
 *
 * Gestiona el registro, inicio y cierre de sesión de usuarios
 * usando Laravel Sanctum para autenticación basada en tokens.
 *
 * Rutas asociadas (públicas):
 *   POST /api/login    → login()
 *   POST /api/register → register()
 *   POST /api/logout   → logout()  [requiere auth:sanctum]
 */
class AuthController extends Controller
{
    /**
     * Registra un nuevo usuario con rol 'client'.
     * Crea automáticamente un token Sanctum y lo devuelve junto con los datos del usuario.
     *
     * @param  Request $request  Campos requeridos: name, username (email), password. Opcional: phone
     * @return \Illuminate\Http\JsonResponse  201 con { success, user, token }
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|email|max:255|unique:users,email', // username == email
            'password' => 'required|string|min:6',
            'phone'    => 'nullable|string'
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->username, // se guarda en la columna 'email'
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
            'role'     => 'client' // rol por defecto para registros públicos
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => [
                'id'       => $user->id,
                'username' => $user->email, // se expone como 'username' al frontend
                'name'     => $user->name,
                'role'     => $user->role
            ],
            'token' => $token
        ], 201);
    }

    /**
     * Inicia sesión con email y contraseña.
     * Verifica el hash de la contraseña y genera un nuevo token Sanctum.
     *
     * @param  Request $request  Campos requeridos: username (email), password
     * @return \Illuminate\Http\JsonResponse  200 con { success, user, token } | 401 si credenciales incorrectas
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string|email',
            'password' => 'required|string'
        ]);

        // Buscar usuario por email (el campo 'username' del frontend corresponde a 'email' en la BD)
        $user = User::where('email', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => [
                'id'       => $user->id,
                'username' => $user->email,
                'name'     => $user->name,
                'role'     => $user->role
            ],
            'token' => $token
        ], 200);
    }

    /**
     * Cierra la sesión del usuario autenticado.
     * Elimina solo el token actual (no todos los tokens del usuario).
     *
     * @param  Request $request  Requiere token Bearer válido (auth:sanctum)
     * @return \Illuminate\Http\JsonResponse  200 con { success, message }
     */
    public function logout(Request $request)
    {
        // Eliminar únicamente el token con el que se hizo esta petición
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada correctamente'
        ]);
    }
}
