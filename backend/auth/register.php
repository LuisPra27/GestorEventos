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

$nombre = $input['nombre'] ?? '';
$email = $input['email'] ?? '';
$telefono = $input['telefono'] ?? '';
$password = $input['password'] ?? '';
$confirm_password = $input['confirm_password'] ?? '';

// Validaciones
if (empty($nombre) || empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Nombre, email y contraseña son requeridos']);
    exit;
}

if ($password !== $confirm_password) {
    http_response_code(400);
    echo json_encode(['error' => 'Las contraseñas no coinciden']);
    exit;
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['error' => 'La contraseña debe tener al menos 6 caracteres']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Email inválido']);
    exit;
}

$userModel = new User();

// Verificar si el email ya existe
$existingUser = $userModel->findByEmail($email);
if ($existingUser) {
    http_response_code(409);
    echo json_encode(['error' => 'Este email ya está registrado']);
    exit;
}

// Crear usuario
$result = $userModel->register($nombre, $email, $telefono, $password, 1); // rol_id = 1 (cliente)

if ($result) {
    echo json_encode([
        'success' => true,
        'message' => 'Usuario registrado exitosamente',
        'redirect' => 'login.html'
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error al registrar usuario']);
}
?>
