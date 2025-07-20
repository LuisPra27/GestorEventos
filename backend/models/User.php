<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private $db;
    private $table = 'usuarios';

    public function __construct() {
        $this->db = new Database();
    }

    public function register($nombre, $email, $telefono, $password, $rol_id = 1) {
        $sql = "INSERT INTO {$this->table} (nombre, email, telefono, password, rol_id) VALUES (?, ?, ?, ?, ?) RETURNING id";
        $stmt = $this->db->prepare($sql);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $result = $stmt->execute([$nombre, $email, $telefono, $hashedPassword, $rol_id]);
        
        if ($result) {
            return $stmt->fetch()['id']; // Devolver el ID del nuevo usuario
        }
        
        return false;
    }

    public function login($email, $password) {
        $sql = "SELECT u.*, r.nombre as rol_nombre FROM {$this->table} u 
                JOIN roles r ON u.rol_id = r.id 
                WHERE u.email = ? AND u.activo = true";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']); // No devolver la contraseña
            return $user;
        }
        
        return false;
    }

    public function findById($id) {
        $sql = "SELECT u.*, r.nombre as rol_nombre FROM {$this->table} u 
                JOIN roles r ON u.rol_id = r.id 
                WHERE u.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        
        $user = $stmt->fetch();
        if ($user) {
            unset($user['password']);
        }
        
        return $user;
    }

    public function findByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        
        return $stmt->fetch();
    }

    public function getAllEmployees() {
        $sql = "SELECT u.*, r.nombre as rol_nombre FROM {$this->table} u 
                JOIN roles r ON u.rol_id = r.id 
                WHERE u.rol_id IN (2, 3) AND u.activo = true
                ORDER BY u.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function updateProfile($id, $nombre, $telefono) {
        $sql = "UPDATE {$this->table} SET nombre = ?, telefono = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([$nombre, $telefono, $id]);
    }

    public function getAll() {
        $sql = "SELECT u.*, r.nombre as rol_nombre FROM {$this->table} u 
                JOIN roles r ON u.rol_id = r.id 
                ORDER BY u.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        $users = $stmt->fetchAll();
        
        // Remover contraseñas de la respuesta
        foreach ($users as &$user) {
            unset($user['password']);
        }
        
        return $users;
    }

    public function create($nombre, $email, $password, $rol_id = 1, $telefono = null) {
        $sql = "INSERT INTO {$this->table} (nombre, email, telefono, password, rol_id) VALUES (?, ?, ?, ?, ?) RETURNING id";
        $stmt = $this->db->prepare($sql);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $result = $stmt->execute([$nombre, $email, $telefono, $hashedPassword, $rol_id]);
        
        if ($result) {
            return $stmt->fetch()['id'];
        }
        
        return false;
    }

    public function update($id, $nombre, $email, $password = null, $rol_id = 1, $telefono = null, $activo = true) {
        if ($password) {
            $sql = "UPDATE {$this->table} SET nombre = ?, email = ?, telefono = ?, password = ?, rol_id = ?, activo = ? WHERE id = ?";
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$nombre, $email, $telefono, $hashedPassword, $rol_id, $activo, $id]);
        } else {
            $sql = "UPDATE {$this->table} SET nombre = ?, email = ?, telefono = ?, rol_id = ?, activo = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$nombre, $email, $telefono, $rol_id, $activo, $id]);
        }
    }

    public function toggleStatus($id) {
        $sql = "UPDATE {$this->table} SET activo = NOT activo WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([$id]);
    }
}
?>
