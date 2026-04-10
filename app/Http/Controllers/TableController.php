<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        return response()->json(Table::with('zone')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required|integer',
            'zone_id' => 'required|exists:zones,id',
            'capacity' => 'required|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $table = Table::create($request->all());

        return response()->json($table->load('zone'), 201);
    }

    public function show($id)
    {
        return response()->json(Table::with('zone')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $table = Table::findOrFail($id);
        
        $request->validate([
            'number' => 'sometimes|integer',
            'zone_id' => 'sometimes|exists:zones,id',
            'capacity' => 'sometimes|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $table->update($request->all());

        return response()->json($table->load('zone'));
    }

    public function destroy($id)
    {
        $table = Table::findOrFail($id);
        $table->delete();

        return response()->json(['message' => 'Mesa eliminada'], 204);
    }
}
