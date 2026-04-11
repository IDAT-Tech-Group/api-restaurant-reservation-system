<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo TimeSlot - Turno horario
 *
 * Representa un bloque de tiempo disponible para realizar reservas.
 * Se usa como referencia en el formulario de reservas del frontend
 * para mostrar los horarios disponibles del restaurante.
 *
 * Columnas:
 *   - start_time: hora de inicio del turno (formato HH:MM, ej: '13:00')
 *   - duration:   duración del turno en minutos (ej: 90)
 */
class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = ['start_time', 'duration'];
}
