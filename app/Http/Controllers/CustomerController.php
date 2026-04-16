<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $clientes = User::where('role', 'client')->get();
        return response()->json($clientes);
    }

    public function updateProfile(Request $request, $id)
    {
        $user = User::where('role', 'client')->findOrFail($id);
        
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string'
        ]);

        $user->update($request->only('name', 'phone'));

        return response()->json($user);
    }
}
