<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../models/User.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

// Verificar que sea gerente
if ($_SESSION['user_role'] != 3) {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso denegado']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$userModel = new User();

switch ($method) {
    case 'GET':
        $users = $userModel->getAll();
        echo json_encode(['success' => true, 'data' => $users]);
        break;
        
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        
        $nombre = $input['nombre'] ?? '';
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';
        $rol_id = $input['rol_id'] ?? 1;
        $telefono = $input['telefono'] ?? null;
        
        if (empty($nombre) || empty($email) || empty($password)) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos incompletos']);
            exit;
        }
        
        // Verificar si el email ya existe
        if ($userModel->findByEmail($email)) {
            http_response_code(400);
            echo json_encode(['error' => 'El email ya está registrado']);
            exit;
        }
        
        $result = $userModel->create($nombre, $email, $password, $rol_id, $telefono);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Usuario creado exitosamente']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al crear usuario']);
        }
        break;
        
    case 'PUT':
        $input = json_decode(file_get_contents('php://input'), true);
        $action = $input['action'] ?? '';
        
        if ($action === 'toggle_status') {
            $userId = $input['id'] ?? 0;
            
            if (!$userId) {
                http_response_code(400);
                echo json_encode(['error' => 'ID de usuario requerido']);
                exit;
            }
            
            $user = $userModel->findById($userId);
            if (!$user) {
                http_response_code(404);
                echo json_encode(['error' => 'Usuario no encontrado']);
                exit;
            }
            
            $result = $userModel->toggleStatus($userId);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Estado actualizado exitosamente']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al actualizar estado']);
            }
        } else {
            // Actualizar usuario completo
            $id = $input['id'] ?? 0;
            $nombre = $input['nombre'] ?? '';
            $email = $input['email'] ?? '';
            $rol_id = $input['rol_id'] ?? 1;
            $telefono = $input['telefono'] ?? null;
            $activo = $input['activo'] ?? true;
            
            if (!$id || empty($nombre) || empty($email)) {
                http_response_code(400);
                echo json_encode(['error' => 'Datos incompletos']);
                exit;
            }
            
            // Si se proporciona nueva contraseña
            $password = !empty($input['password']) ? $input['password'] : null;
            
            $result = $userModel->update($id, $nombre, $email, $password, $rol_id, $telefono, $activo);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Usuario actualizado exitosamente']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al actualizar usuario']);
            }
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}
?>
