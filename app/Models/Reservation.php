<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Reservation - Reserva de mesa
 *
 * Representa una reserva de mesa en el restaurante.
 * Pertenece a un usuario (user_id) y a una mesa (table_id).
 *
 * Columnas:
 *   - user_id:    FK → users.id  (usuario que hizo la reserva)
 *   - table_id:   FK → tables.id (mesa reservada)
 *   - date:       fecha de la reserva (YYYY-MM-DD)
 *   - start_time: hora de inicio (HH:MM)
 *   - end_time:   hora de fin (HH:MM)
 *   - persons:    cantidad de personas
 *   - status:     estado: pendiente | reservado | completado | cancelado
 *   - tipopago:   tipo de pago (opcional)
 *   - notes:      notas adicionales del cliente (opcional)
 *
 * Relaciones:
 *   - belongsTo User  → reservation->user
 *   - belongsTo Table → reservation->table (incluye table->zone con eager load)
 */
class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'table_id',
        'date',
        'start_time',
        'end_time',
        'persons',
        'status',
        'tipopago',
        'notes'
    ];

    /**
     * Usuario propietario de la reserva.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mesa asociada a la reserva.
     * Se puede cargar con su zona usando: with('table.zone')
     */
    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
