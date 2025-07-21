<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $user = User::with('role')->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales inválidas'
            ], 401);
        }

        if (!$user->activo) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario inactivo'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $redirect = match($user->rol_id) {
            1 => 'dashboard-cliente.html',
            2 => 'dashboard-empleado.html',
            3 => 'dashboard-gerente.html',
            default => 'login.html'
        };

        return response()->json([
            'success' => true,
            'message' => 'Login exitoso',
            'user' => [
                'id' => $user->id,
                'nombre' => $user->nombre,
                'email' => $user->email,
                'rol_id' => $user->rol_id,
                'rol_nombre' => $user->role->nombre
            ],
            'token' => $token,
            'redirect' => $redirect
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout exitoso'
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load('role');

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'nombre' => $user->nombre,
                'email' => $user->email,
                'telefono' => $user->telefono,
                'rol_id' => $user->rol_id,
                'rol_nombre' => $user->role->nombre,
                'activo' => $user->activo
            ]
        ]);
    }
}
