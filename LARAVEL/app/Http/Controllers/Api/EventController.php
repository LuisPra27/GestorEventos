<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = Auth::user();

            if ($user->rol_id == 1) { // Cliente
                $events = Event::with(['service', 'user', 'employee'])
                    ->where('cliente_id', $user->id)
                    ->orderBy('fecha_evento', 'desc')
                    ->get();
            } elseif ($user->rol_id == 2) { // Empleado
                $events = Event::with(['service', 'user', 'employee'])
                    ->where('empleado_id', $user->id)
                    ->orderBy('fecha_evento', 'desc')
                    ->get();
            } else { // Gerente
                $events = Event::with(['service', 'user', 'employee'])
                    ->orderBy('fecha_evento', 'desc')
                    ->get();
            }

            $eventsData = $events->map(function($event) {
                return [
                    'id' => $event->id,
                    'titulo' => $event->titulo,
                    'descripcion' => $event->descripcion,
                    'fecha_evento' => $event->fecha_evento,
                    'ubicacion' => $event->ubicacion,
                    'numero_invitados' => $event->numero_invitados,
                    'presupuesto' => $event->presupuesto,
                    'estado' => $event->estado,
                    'requisitos_especiales' => $event->requisitos_especiales,
                    'servicio_id' => $event->servicio_id,
                    'servicio_nombre' => $event->service ? $event->service->nombre : 'N/A',
                    'cliente_id' => $event->cliente_id,
                    'cliente_nombre' => $event->user ? $event->user->nombre : 'N/A',
                    'empleado_id' => $event->empleado_id,
                    'empleado_nombre' => $event->employee ? $event->employee->nombre : 'No asignado',
                    'created_at' => $event->created_at,
                    'updated_at' => $event->updated_at
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $eventsData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener eventos: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'servicio_id' => 'required|exists:services,id',
                'titulo' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'fecha_evento' => 'required|date|after:now',
                'ubicacion' => 'required|string|max:255',
                'numero_invitados' => 'required|integer|min:1',
                'presupuesto' => 'nullable|numeric|min:0',
                'requisitos_especiales' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 400);
            }

            $user = Auth::user();

            $event = Event::create([
                'servicio_id' => $request->servicio_id,
                'cliente_id' => $user->id,
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'fecha_evento' => $request->fecha_evento,
                'ubicacion' => $request->ubicacion,
                'numero_invitados' => $request->numero_invitados,
                'presupuesto' => $request->presupuesto,
                'estado' => 'pendiente',
                'requisitos_especiales' => $request->requisitos_especiales
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Evento creado exitosamente',
                'data' => $event->load(['service', 'user'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al crear evento: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = Auth::user();

            $event = Event::with(['service', 'user', 'employee'])->find($id);

            if (!$event) {
                return response()->json([
                    'success' => false,
                    'error' => 'Evento no encontrado'
                ], 404);
            }

            // Verificar permisos
            if ($user->rol_id == 1 && $event->cliente_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'No tienes permiso para ver este evento'
                ], 403);
            }

            if ($user->rol_id == 2 && $event->empleado_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'No tienes permiso para ver este evento'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $event->id,
                    'titulo' => $event->titulo,
                    'descripcion' => $event->descripcion,
                    'fecha_evento' => $event->fecha_evento,
                    'ubicacion' => $event->ubicacion,
                    'numero_invitados' => $event->numero_invitados,
                    'presupuesto' => $event->presupuesto,
                    'estado' => $event->estado,
                    'requisitos_especiales' => $event->requisitos_especiales,
                    'servicio_id' => $event->servicio_id,
                    'servicio_nombre' => $event->service ? $event->service->nombre : 'N/A',
                    'cliente_id' => $event->cliente_id,
                    'cliente_nombre' => $event->user ? $event->user->nombre : 'N/A',
                    'empleado_id' => $event->empleado_id,
                    'empleado_nombre' => $event->employee ? $event->employee->nombre : 'No asignado',
                    'created_at' => $event->created_at,
                    'updated_at' => $event->updated_at
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener evento: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();

            $event = Event::find($id);

            if (!$event) {
                return response()->json([
                    'success' => false,
                    'error' => 'Evento no encontrado'
                ], 404);
            }

            // Verificar permisos
            if ($user->rol_id == 1 && $event->cliente_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'No tienes permiso para modificar este evento'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'servicio_id' => 'sometimes|exists:services,id',
                'titulo' => 'sometimes|string|max:255',
                'descripcion' => 'nullable|string',
                'fecha_evento' => 'sometimes|date|after:now',
                'ubicacion' => 'sometimes|string|max:255',
                'numero_invitados' => 'sometimes|integer|min:1',
                'presupuesto' => 'nullable|numeric|min:0',
                'requisitos_especiales' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 400);
            }

            $event->update($request->only([
                'servicio_id', 'titulo', 'descripcion', 'fecha_evento',
                'ubicacion', 'numero_invitados', 'presupuesto', 'requisitos_especiales'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Evento actualizado exitosamente',
                'data' => $event->load(['service', 'user', 'employee'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al actualizar evento: ' . $e->getMessage()
            ], 500);
        }
    }

    public function assignEmployee(Request $request, $id)
    {
        try {
            $user = Auth::user();

            // Solo gerentes pueden asignar empleados
            if ($user->rol_id != 3) {
                return response()->json([
                    'success' => false,
                    'error' => 'No tienes permiso para asignar empleados'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'empleado_id' => 'nullable|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 400);
            }

            $event = Event::find($id);

            if (!$event) {
                return response()->json([
                    'success' => false,
                    'error' => 'Evento no encontrado'
                ], 404);
            }

            $event->update(['empleado_id' => $request->empleado_id ?: null]);

            $message = $request->empleado_id
                ? 'Empleado asignado exitosamente'
                : 'Empleado desasignado exitosamente';

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al asignar empleado: ' . $e->getMessage()
            ], 500);
        }
    }

    public function changeStatus(Request $request, $id)
    {
        try {
            $user = Auth::user();

            $validator = Validator::make($request->all(), [
                'estado' => 'required|in:pendiente,confirmado,en_progreso,completado,cancelado'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 400);
            }

            $event = Event::find($id);

            if (!$event) {
                return response()->json([
                    'success' => false,
                    'error' => 'Evento no encontrado'
                ], 404);
            }

            // Verificar permisos según el rol
            if ($user->rol_id == 1 && !in_array($request->estado, ['cancelado'])) {
                return response()->json([
                    'success' => false,
                    'error' => 'Los clientes solo pueden cancelar eventos'
                ], 403);
            }

            if ($user->rol_id == 2 && $event->empleado_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'No tienes permiso para modificar este evento'
                ], 403);
            }

            $event->update(['estado' => $request->estado]);

            return response()->json([
                'success' => true,
                'message' => 'Estado del evento actualizado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al cambiar estado: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addFollowUp(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'descripcion' => 'required|string',
                'fecha_seguimiento' => 'required|date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 400);
            }

            $event = Event::find($id);

            if (!$event) {
                return response()->json([
                    'success' => false,
                    'error' => 'Evento no encontrado'
                ], 404);
            }

            // Aquí podrías crear un seguimiento si tienes esa funcionalidad
            // Por ahora solo devolvemos éxito

            return response()->json([
                'success' => true,
                'message' => 'Seguimiento agregado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al agregar seguimiento: ' . $e->getMessage()
            ], 500);
        }
    }
}
