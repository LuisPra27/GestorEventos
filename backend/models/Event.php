<?php
require_once __DIR__ . '/../config/database.php';

class Event {
    private $db;
    private $table = 'eventos';

    public function __construct() {
        $this->db = new Database();
    }

    public function create($cliente_id, $servicio_id, $titulo, $descripcion, $fecha_evento, $ubicacion, $numero_invitados, $presupuesto, $notas_especiales = '') {
        $sql = "INSERT INTO {$this->table} (cliente_id, servicio_id, titulo, descripcion, fecha_evento, ubicacion, numero_invitados, presupuesto, notas_especiales) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) RETURNING id";
        $stmt = $this->db->prepare($sql);
        
        $result = $stmt->execute([
            $cliente_id, 
            $servicio_id, 
            $titulo, 
            $descripcion, 
            $fecha_evento, 
            $ubicacion, 
            $numero_invitados, 
            $presupuesto, 
            $notas_especiales
        ]);
        
        if ($result) {
            return $stmt->fetch()['id']; // Devolver el ID del nuevo evento
        }
        
        return false;
    }

    public function getByClientId($cliente_id) {
        $sql = "SELECT e.*, s.nombre as servicio_nombre, s.precio as servicio_precio,
                       emp.nombre as empleado_nombre
                FROM {$this->table} e
                JOIN servicios s ON e.servicio_id = s.id
                LEFT JOIN usuarios emp ON e.empleado_id = emp.id
                WHERE e.cliente_id = ?
                ORDER BY e.fecha_evento DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$cliente_id]);
        
        return $stmt->fetchAll();
    }

    public function getByEmployeeId($empleado_id) {
        $sql = "SELECT e.*, s.nombre as servicio_nombre, s.precio as servicio_precio,
                       c.nombre as cliente_nombre, c.email as cliente_email, c.telefono as cliente_telefono
                FROM {$this->table} e
                JOIN servicios s ON e.servicio_id = s.id
                JOIN usuarios c ON e.cliente_id = c.id
                WHERE e.empleado_id = ?
                ORDER BY e.fecha_evento ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$empleado_id]);
        
        return $stmt->fetchAll();
    }

    public function getAll() {
        $sql = "SELECT e.*, s.nombre as servicio_nombre, s.precio as servicio_precio,
                       c.nombre as cliente_nombre, c.email as cliente_email, c.telefono as cliente_telefono,
                       emp.nombre as empleado_nombre
                FROM {$this->table} e
                JOIN servicios s ON e.servicio_id = s.id
                JOIN usuarios c ON e.cliente_id = c.id
                LEFT JOIN usuarios emp ON e.empleado_id = emp.id
                ORDER BY e.fecha_evento DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function getAllForManager() {
        // Alias para getAll() con información adicional para gerentes
        return $this->getAll();
    }

    public function findById($id) {
        $sql = "SELECT e.*, s.nombre as servicio_nombre, s.precio as servicio_precio, s.descripcion as servicio_descripcion,
                       c.nombre as cliente_nombre, c.email as cliente_email, c.telefono as cliente_telefono,
                       emp.nombre as empleado_nombre
                FROM {$this->table} e
                JOIN servicios s ON e.servicio_id = s.id
                JOIN usuarios c ON e.cliente_id = c.id
                LEFT JOIN usuarios emp ON e.empleado_id = emp.id
                WHERE e.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch();
    }

    public function assignEmployee($evento_id, $empleado_id) {
        $sql = "UPDATE {$this->table} SET empleado_id = ?, estado = 'confirmado' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([$empleado_id, $evento_id]);
    }

    public function updateStatus($evento_id, $estado) {
        $sql = "UPDATE {$this->table} SET estado = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([$estado, $evento_id]);
    }

    public function addFollowUp($evento_id, $usuario_id, $comentario, $tipo = 'comentario') {
        $sql = "INSERT INTO seguimientos (evento_id, usuario_id, comentario, tipo) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([$evento_id, $usuario_id, $comentario, $tipo]);
    }

    public function getFollowUps($evento_id) {
        $sql = "SELECT s.*, u.nombre as usuario_nombre, u.rol_id
                FROM seguimientos s
                JOIN usuarios u ON s.usuario_id = u.id
                WHERE s.evento_id = ?
                ORDER BY s.created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$evento_id]);
        
        return $stmt->fetchAll();
    }
}
?>
