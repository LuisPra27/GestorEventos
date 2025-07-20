<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../models/User.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    $input = $_POST;
}

$email = $input['email'] ?? '';
$password = $input['password'] ?? '';

if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Email y contraseña son requeridos']);
    exit;
}

$userModel = new User();
$user = $userModel->login($email, $password);

if ($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['nombre'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['rol_id'];
    $_SESSION['user_role_name'] = $user['rol_nombre'];
    
    echo json_encode([
        'success' => true,
        'message' => 'Login exitoso',
        'user' => [
            'id' => $user['id'],
            'nombre' => $user['nombre'],
            'email' => $user['email'],
            'rol_id' => $user['rol_id'],
            'rol_nombre' => $user['rol_nombre']
        ],
        'redirect' => $user['rol_id'] == 1 ? 'dashboard-cliente.html' : 
                     ($user['rol_id'] == 2 ? 'dashboard-empleado.html' : 'dashboard-gerente.html')
    ]);
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Credenciales inválidas']);
}
?>
