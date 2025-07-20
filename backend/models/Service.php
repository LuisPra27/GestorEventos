<?php
require_once __DIR__ . '/../config/database.php';

class Service {
    private $db;
    private $table = 'servicios';

    public function __construct() {
        $this->db = new Database();
    }

    public function getAll() {
        $sql = "SELECT * FROM {$this->table} WHERE activo = true ORDER BY nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND activo = true";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch();
    }

    public function create($nombre, $descripcion, $precio, $duracion_horas = 4) {
        $sql = "INSERT INTO {$this->table} (nombre, descripcion, precio, duracion_horas) VALUES (?, ?, ?, ?) RETURNING id";
        $stmt = $this->db->prepare($sql);
        
        $result = $stmt->execute([$nombre, $descripcion, $precio, $duracion_horas]);
        
        if ($result) {
            return $stmt->fetch()['id']; // Devolver el ID del nuevo servicio
        }
        
        return false;
    }

    public function update($id, $nombre, $descripcion, $precio, $duracion_horas, $activo = true) {
        $sql = "UPDATE {$this->table} SET nombre = ?, descripcion = ?, precio = ?, duracion_horas = ?, activo = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([$nombre, $descripcion, $precio, $duracion_horas, $activo, $id]);
    }

    public function delete($id) {
        $sql = "UPDATE {$this->table} SET activo = false WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([$id]);
    }

    public function toggleStatus($id) {
        $sql = "UPDATE {$this->table} SET activo = NOT activo WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([$id]);
    }
}
?>
