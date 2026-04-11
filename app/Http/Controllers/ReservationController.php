<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::with(['user', 'table.zone']);

        $authUser = $request->user();

        // Non-admin users only see their own reservations
        if ($authUser && $authUser->role !== 'admin') {
            $query->where('user_id', $authUser->id);
        }

        if ($request->has('date')) {
            $query->where('date', $request->date);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('user_id') && $authUser && $authUser->role === 'admin') {
            $query->where('user_id', $request->user_id);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'persons' => 'required|integer|min:1',
            'tipopago' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $table = Table::findOrFail($request->table_id);
        if ($request->persons > $table->capacity) {
            return response()->json(['message' => 'La cantidad de personas supera la capacidad de la mesa'], 422);
        }

        // Logic check overlapping schedules
        $conflict = Reservation::where('table_id', $request->table_id)
            ->where('date', $request->date)
            ->where(function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    // Check if new reservation starts inside an existing one
                    $q->where('start_time', '<=', $request->start_time)
                      ->where('end_time', '>', $request->start_time);
                })->orWhere(function ($q) use ($request) {
                    // Check if new reservation ends inside an existing one
                    $q->where('start_time', '<', $request->end_time)
                      ->where('end_time', '>=', $request->end_time);
                })->orWhere(function ($q) use ($request) {
                    // Check if new reservation fully consumes an existing one
                    $q->where('start_time', '>=', $request->start_time)
                      ->where('end_time', '<=', $request->end_time);
                });
            })
            ->where('status', '!=', 'cancelado') // allow overlap if canceled
            ->exists();

        if ($conflict) {
            return response()->json(['message' => 'La mesa no está disponible en este horario'], 422);
        }

        $data = $request->all();
        if ($request->user()) {
            $data['user_id'] = $request->user()->id;
        }

        $res = Reservation::create($data);

        return response()->json($res->load(['user', 'table.zone']), 201);
    }
    
    public function show($id)
    {
        return response()->json(Reservation::with(['user', 'table.zone'])->findOrFail($id));
    }

    public function updateStatus(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        
        $request->validate([
            'status' => 'required|string|in:pendiente,reservado,completado,cancelado'
        ]);

        $reservation->update(['status' => $request->status]);

        return response()->json($reservation->load(['user', 'table.zone']));
    }

    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();

        return response()->json(['message' => 'Reserva eliminada'], 204);
    }
}
