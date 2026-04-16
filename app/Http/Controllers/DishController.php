<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use Illuminate\Http\Request;

class DishController extends Controller
{
    public function index()
    {
        return response()->json(Dish::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'emoji' => 'nullable|string',
            'category' => 'nullable|string',
            'status' => 'boolean'
        ]);

        $dish = Dish::create($request->all());

        return response()->json($dish, 201);
    }

    public function show($id)
    {
        return response()->json(Dish::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $dish = Dish::findOrFail($id);
        
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'emoji' => 'nullable|string',
            'category' => 'nullable|string',
            'status' => 'boolean'
        ]);

        $dish->update($request->all());

        return response()->json($dish);
    }

    public function destroy($id)
    {
        $dish = Dish::findOrFail($id);
        $dish->delete();

        return response()->json(['message' => 'Plato eliminado'], 204);
    }
}
