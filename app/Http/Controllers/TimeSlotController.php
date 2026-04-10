<?php

namespace App\Http\Controllers;

use App\Models\TimeSlot;
use Illuminate\Http\Request;

class TimeSlotController extends Controller
{
    public function index()
    {
        return response()->json(TimeSlot::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'start_time' => 'required|date_format:H:i',
            'duration' => 'required|integer|min:1'
        ]);

        $slot = TimeSlot::create($request->all());

        return response()->json($slot, 201);
    }

    public function show($id)
    {
        return response()->json(TimeSlot::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $slot = TimeSlot::findOrFail($id);
        
        $request->validate([
            'start_time' => 'sometimes|date_format:H:i',
            'duration' => 'sometimes|integer|min:1'
        ]);

        $slot->update($request->all());

        return response()->json($slot);
    }

    public function destroy($id)
    {
        $slot = TimeSlot::findOrFail($id);
        $slot->delete();

        return response()->json(['message' => 'Turno eliminado'], 204);
    }
}
