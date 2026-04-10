<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    public function index()
    {
        return response()->json(Zone::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string'
        ]);

        $zone = Zone::create($request->all());

        return response()->json($zone, 201);
    }

    public function show($id)
    {
        return response()->json(Zone::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $zone = Zone::findOrFail($id);
        
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'icon' => 'nullable|string'
        ]);

        $zone->update($request->all());

        return response()->json($zone);
    }

    public function destroy($id)
    {
        $zone = Zone::findOrFail($id);
        $zone->delete();

        return response()->json(['message' => 'Zona eliminada'], 204);
    }
}
