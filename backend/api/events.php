<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/User.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$eventModel = new Event();
$userModel = new User();

switch ($method) {
    case 'GET':
        $action = $_GET['action'] ?? 'list';
        
        if ($action === 'list') {
            // Obtener eventos según el rol del usuario
            if ($_SESSION['user_role'] == 1) { // Cliente
                $events = $eventModel->getByClientId($_SESSION['user_id']);
            } elseif ($_SESSION['user_role'] == 2) { // Empleado
                $events = $eventModel->getByEmployeeId($_SESSION['user_id']);
            } else { // Gerente
                $events = $eventModel->getAllForManager();
            }
            
            echo json_encode(['success' => true, 'data' => $events]);
            
        } elseif ($action === 'all' && $_SESSION['user_role'] == 3) {
            // Solo gerentes pueden usar esta acción
            $events = $eventModel->getAllForManager();
            echo json_encode(['success' => true, 'data' => $events]);
            
        } elseif ($action === 'details') {
            $event_id = $_GET['id'] ?? null;
            
            if (!$event_id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID del evento requerido']);
                exit;
            }
            
            $event = $eventModel->findById($event_id);
            
            if (!$event) {
                http_response_code(404);
                echo json_encode(['error' => 'Evento no encontrado']);
                exit;
            }
            
            // Verificar permisos
            if ($_SESSION['user_role'] == 1 && $event['cliente_id'] != $_SESSION['user_id']) {
                http_response_code(403);
                echo json_encode(['error' => 'Acceso denegado']);
                exit;
            }
            
            if ($_SESSION['user_role'] == 2 && $event['empleado_id'] != $_SESSION['user_id']) {
                http_response_code(403);
                echo json_encode(['error' => 'Acceso denegado']);
                exit;
            }
            
            // Obtener seguimientos
            $followUps = $eventModel->getFollowUps($event_id);
            $event['seguimientos'] = $followUps;
            
            echo json_encode(['success' => true, 'data' => $event]);
            
        } elseif ($action === 'employees') {
            // Solo gerentes pueden ver empleados
            if ($_SESSION['user_role'] != 3) {
                http_response_code(403);
                echo json_encode(['error' => 'Acceso denegado']);
                exit;
            }
            
            $employees = $userModel->getAllEmployees();
            echo json_encode(['success' => true, 'data' => $employees]);
        }
        break;
        
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        $action = $input['action'] ?? 'create';
        
        if ($action === 'create') {
            // Solo clientes pueden crear eventos
            if ($_SESSION['user_role'] != 1) {
                http_response_code(403);
                echo json_encode(['error' => 'Solo los clientes pueden crear eventos']);
                exit;
            }
            
            $servicio_id = $input['servicio_id'] ?? null;
            $titulo = $input['titulo'] ?? '';
            $descripcion = $input['descripcion'] ?? '';
            $fecha_evento = $input['fecha_evento'] ?? '';
            $ubicacion = $input['ubicacion'] ?? '';
            $numero_invitados = $input['numero_invitados'] ?? 50;
            $presupuesto = $input['presupuesto'] ?? 0;
            $notas_especiales = $input['notas_especiales'] ?? '';
            
            if (!$servicio_id || empty($titulo) || empty($fecha_evento) || empty($ubicacion)) {
                http_response_code(400);
                echo json_encode(['error' => 'Campos requeridos: servicio, título, fecha y ubicación']);
                exit;
            }
            
            $result = $eventModel->create(
                $_SESSION['user_id'],
                $servicio_id,
                $titulo,
                $descripcion,
                $fecha_evento,
                $ubicacion,
                $numero_invitados,
                $presupuesto,
                $notas_especiales
            );
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Evento creado exitosamente']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al crear evento']);
            }
            
        } elseif ($action === 'assign_employee') {
            // Solo gerentes pueden asignar empleados
            if ($_SESSION['user_role'] != 3) {
                http_response_code(403);
                echo json_encode(['error' => 'Acceso denegado']);
                exit;
            }
            
            $evento_id = $input['evento_id'] ?? null;
            $empleado_id = $input['empleado_id'] ?? null;
            
            if (!$evento_id || !$empleado_id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID del evento y empleado requeridos']);
                exit;
            }
            
            $result = $eventModel->assignEmployee($evento_id, $empleado_id);
            
            if ($result) {
                // Agregar seguimiento
                $eventModel->addFollowUp($evento_id, $_SESSION['user_id'], 'Empleado asignado al evento', 'cambio_estado');
                echo json_encode(['success' => true, 'message' => 'Empleado asignado exitosamente']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al asignar empleado']);
            }
            
        } elseif ($action === 'add_followup') {
            $evento_id = $input['evento_id'] ?? null;
            $comentario = $input['comentario'] ?? '';
            $tipo = $input['tipo'] ?? 'comentario';
            
            if (!$evento_id || empty($comentario)) {
                http_response_code(400);
                echo json_encode(['error' => 'ID del evento y comentario requeridos']);
                exit;
            }
            
            // Verificar permisos
            $event = $eventModel->findById($evento_id);
            if (!$event) {
                http_response_code(404);
                echo json_encode(['error' => 'Evento no encontrado']);
                exit;
            }
            
            if ($_SESSION['user_role'] == 1 && $event['cliente_id'] != $_SESSION['user_id']) {
                http_response_code(403);
                echo json_encode(['error' => 'Acceso denegado']);
                exit;
            }
            
            if ($_SESSION['user_role'] == 2 && $event['empleado_id'] != $_SESSION['user_id']) {
                http_response_code(403);
                echo json_encode(['error' => 'Acceso denegado']);
                exit;
            }
            
            $result = $eventModel->addFollowUp($evento_id, $_SESSION['user_id'], $comentario, $tipo);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Comentario agregado exitosamente']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al agregar comentario']);
            }
        }
        break;
        
    case 'PUT':
        $input = json_decode(file_get_contents('php://input'), true);
        $action = $input['action'] ?? 'update_status';
        
        if ($action === 'update_status') {
            $evento_id = $input['evento_id'] ?? null;
            $estado = $input['estado'] ?? '';
            
            if (!$evento_id || empty($estado)) {
                http_response_code(400);
                echo json_encode(['error' => 'ID del evento y estado requeridos']);
                exit;
            }
            
            // Solo empleados y gerentes pueden cambiar estados
            if ($_SESSION['user_role'] == 1) {
                http_response_code(403);
                echo json_encode(['error' => 'Acceso denegado']);
                exit;
            }
            
            $result = $eventModel->updateStatus($evento_id, $estado);
            
            if ($result) {
                // Agregar seguimiento
                $eventModel->addFollowUp($evento_id, $_SESSION['user_id'], "Estado cambiado a: $estado", 'cambio_estado');
                echo json_encode(['success' => true, 'message' => 'Estado actualizado exitosamente']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al actualizar estado']);
            }
            
        } elseif ($action === 'assign_employee') {
            // Solo gerentes pueden asignar empleados
            if ($_SESSION['user_role'] != 3) {
                http_response_code(403);
                echo json_encode(['error' => 'Solo gerentes pueden asignar empleados']);
                exit;
            }
            
            $event_id = $input['event_id'] ?? null;
            $employee_id = $input['employee_id'] ?? null;
            $estado = $input['estado'] ?? null;
            $notas = $input['notas'] ?? '';
            
            if (!$event_id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID del evento requerido']);
                exit;
            }
            
            try {
                // Actualizar la asignación del empleado
                $result = $eventModel->assignEmployee($event_id, $employee_id);
                
                // Si también se cambió el estado, actualizarlo
                if ($estado && $result) {
                    $eventModel->updateStatus($event_id, $estado);
                }
                
                // Agregar seguimiento
                if ($result) {
                    $employeeName = $employee_id ? $userModel->findById($employee_id)['nombre'] : 'Sin asignar';
                    $message = $employee_id ? "Empleado asignado: $employeeName" : "Empleado desasignado";
                    if ($notas) {
                        $message .= " - Notas: $notas";
                    }
                    $eventModel->addFollowUp($event_id, $_SESSION['user_id'], $message, 'asignacion');
                    
                    echo json_encode(['success' => true, 'message' => 'Empleado asignado correctamente']);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => 'Error al asignar empleado']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Error al asignar empleado: ' . $e->getMessage()]);
            }
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}
?>
