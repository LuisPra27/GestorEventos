<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../models/Service.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$serviceModel = new Service();

switch ($method) {
    case 'GET':
        $services = $serviceModel->getAll();
        echo json_encode(['success' => true, 'data' => $services]);
        break;
        
    case 'POST':
        // Solo gerentes pueden crear servicios
        if ($_SESSION['user_role'] != 3) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            exit;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        $nombre = $input['nombre'] ?? '';
        $descripcion = $input['descripcion'] ?? '';
        $precio = $input['precio'] ?? 0;
        $duracion_horas = $input['duracion_horas'] ?? 4;
        
        if (empty($nombre) || empty($descripcion) || $precio <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos inválidos']);
            exit;
        }
        
        $result = $serviceModel->create($nombre, $descripcion, $precio, $duracion_horas);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Servicio creado exitosamente']);
        }
        break;
        
    case 'PUT':
        // Solo gerentes pueden modificar servicios
        if ($_SESSION['user_role'] != 3) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            exit;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $action = $input['action'] ?? '';
        
        if ($action === 'toggle_status') {
            $serviceId = $input['id'] ?? 0;
            
            if (!$serviceId) {
                http_response_code(400);
                echo json_encode(['error' => 'ID de servicio requerido']);
                exit;
            }
            
            $result = $serviceModel->toggleStatus($serviceId);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Estado actualizado exitosamente']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al actualizar estado']);
            }
        } else {
            // Actualizar servicio completo
            $id = $input['id'] ?? 0;
            $nombre = $input['nombre'] ?? '';
            $descripcion = $input['descripcion'] ?? '';
            $precio = $input['precio'] ?? 0;
            $duracion_horas = $input['duracion_horas'] ?? 4;
            $activo = $input['activo'] ?? true;
            
            if (!$id || empty($nombre) || $precio <= 0) {
                http_response_code(400);
                echo json_encode(['error' => 'Datos inválidos']);
                exit;
            }
            
            $result = $serviceModel->update($id, $nombre, $descripcion, $precio, $duracion_horas, $activo);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Servicio actualizado exitosamente']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al actualizar servicio']);
            }
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}
?>
