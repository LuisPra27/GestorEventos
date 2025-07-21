<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'nombre' => $user->nombre,
                'email' => $user->email,
                'telefono' => $user->telefono,
                'rol_id' => $user->rol_id,
                'rol_nombre' => $user->role->nombre,
                'activo' => $user->activo,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|string|min:6',
            'telefono' => 'nullable|string',
            'rol_id' => 'required|in:1,2,3',
            'activo' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $user = User::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telefono' => $request->telefono,
            'rol_id' => $request->rol_id,
            'activo' => $request->activo ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado exitosamente',
            'data' => $user->load('role')
        ], 201);
    }

    public function show(User $user)
    {
        return response()->json([
            'success' => true,
            'data' => $user->load('role')
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'string|max:255',
            'email' => 'email|unique:usuarios,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'telefono' => 'nullable|string',
            'rol_id' => 'in:1,2,3',
            'activo' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $updateData = $request->only(['nombre', 'email', 'telefono', 'rol_id', 'activo']);

        if ($request->password) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado exitosamente',
            'data' => $user->load('role')
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado exitosamente'
        ]);
    }

    public function toggleStatus(User $user)
    {
        $user->update(['activo' => !$user->activo]);

        return response()->json([
            'success' => true,
            'message' => 'Estado del usuario actualizado',
            'data' => $user
        ]);
    }
}
