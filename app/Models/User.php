<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Modelo User - Usuario del sistema
 *
 * Representa tanto a clientes como a administradores del restaurante.
 * Usa Laravel Sanctum (HasApiTokens) para autenticación basada en tokens Bearer.
 *
 * Columnas principales:
 *   - name:     nombre completo del usuario
 *   - email:    correo electrónico (usado como username en el login)
 *   - password: contraseña hasheada con bcrypt
 *   - phone:    teléfono de contacto (opcional)
 *   - role:     rol del usuario: 'client' | 'admin'
 *
 * Traits:
 *   - HasApiTokens: habilita la generación de tokens Sanctum (createToken)
 *   - HasFactory:   permite usar factories en tests
 *   - Notifiable:   permite enviar notificaciones (email, SMS, etc.)
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Campos que se pueden asignar masivamente (mass assignment).
     * La contraseña se hashea antes de guardarse en AuthController.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
    ];

    /**
     * Campos excluidos de la serialización JSON.
     * La contraseña nunca se expone en las respuestas del API.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Conversión automática de tipos al acceder a los atributos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
