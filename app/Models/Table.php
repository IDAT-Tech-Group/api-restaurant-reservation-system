<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Table - Mesa del restaurante
 *
 * Representa una mesa física del restaurante.
 * Pertenece a una zona y puede tener múltiples reservas a lo largo del tiempo.
 *
 * Columnas:
 *   - zone_id:   FK → zones.id (zona donde está ubicada la mesa)
 *   - number:    número identificador de la mesa (ej: 1, 2, 5...)
 *   - capacity:  capacidad máxima de personas
 *   - is_active: true = disponible para reservas, false = fuera de servicio
 *
 * Relaciones:
 *   - belongsTo Zone         → table->zone
 *   - hasMany   Reservation  → table->reservations
 */
class Table extends Model
{
    use HasFactory;

    protected $fillable = ['zone_id', 'number', 'capacity', 'is_active'];

    /**
     * Zona donde está ubicada la mesa.
     */
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    /**
     * Todas las reservas asociadas a esta mesa.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
