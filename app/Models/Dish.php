<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Dish - Plato del menú
 *
 * Representa un ítem de la carta del restaurante.
 * No tiene relaciones con otras entidades; es una tabla independiente.
 *
 * Columnas:
 *   - name:        nombre del plato
 *   - description: descripción del plato (opcional)
 *   - price:       precio en la moneda local (decimal)
 *   - emoji:       emoji decorativo para mostrar en la UI (ej: '🍝')
 *   - category:    categoría del plato (ej: 'Entradas', 'Fondos', 'Postres', 'Bebidas')
 *   - status:      true = disponible, false = no disponible temporalmente
 */
class Dish extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'emoji', 'category', 'status'];
}
