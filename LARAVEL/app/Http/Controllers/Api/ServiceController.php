<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    public function index()
    {
        try {
            // Obtener todos los servicios (activos e inactivos) para el panel gerencial
            $services = Service::all();

            return response()->json([
                'success' => true,
                'data' => $services
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener servicios: ' . $e->getMessage()
            ], 500);
        }
    }

    public function indexActive()
    {
        try {
            // Obtener solo servicios activos para clientes
            $services = Service::where('activo', true)->get();

            return response()->json([
                'success' => true,
                'data' => $services
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener servicios activos: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'precio' => 'required|numeric|min:0',
                'duracion_horas' => 'required|integer|min:1',
                'activo' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 400);
            }

            $service = Service::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Servicio creado exitosamente',
                'data' => $service
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al crear servicio: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $service = Service::find($id);

            if (!$service) {
                return response()->json([
                    'success' => false,
                    'error' => 'Servicio no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'nombre' => 'sometimes|string|max:255',
                'descripcion' => 'nullable|string',
                'precio' => 'sometimes|numeric|min:0',
                'duracion_horas' => 'sometimes|integer|min:1',
                'activo' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 400);
            }

            $service->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Servicio actualizado exitosamente',
                'data' => $service
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar servicio: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $service = Service::find($id);

            if (!$service) {
                return response()->json([
                    'success' => false,
                    'error' => 'Servicio no encontrado'
                ], 404);
            }

            $service->delete();

            return response()->json([
                'success' => true,
                'message' => 'Servicio eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al eliminar servicio: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus($id)
    {
        try {
            $service = Service::find($id);

            if (!$service) {
                return response()->json([
                    'success' => false,
                    'error' => 'Servicio no encontrado'
                ], 404);
            }

            $service->update(['activo' => !$service->activo]);

            return response()->json([
                'success' => true,
                'message' => 'Estado del servicio actualizado exitosamente',
                'data' => $service
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al cambiar estado: ' . $e->getMessage()
            ], 500);
        }
    }
}
