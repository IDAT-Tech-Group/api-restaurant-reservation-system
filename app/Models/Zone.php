<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Zone - Zona del restaurante
 *
 * Representa un área o sección del restaurante (ej: Salón Principal, Terraza, VIP).
 * Agrupa un conjunto de mesas bajo un mismo espacio físico.
 *
 * Columnas:
 *   - name: nombre de la zona (ej: 'Terraza')
 *   - icon: emoji representativo para mostrar en la UI (ej: '🌅')
 *
 * Relaciones:
 *   - hasMany Table → zone->tables
 */
class Zone extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'icon'];

    /**
     * Mesas que pertenecen a esta zona.
     */
    public function tables()
    {
        return $this->hasMany(Table::class);
    }
}
