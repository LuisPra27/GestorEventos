<?php
class Database {
    private $host = 'localhost';
    private $database = 'eventos';
    private $username = 'postgres';
    private $password = 'luis123'; // Cambiar por tu contraseña de PostgreSQL
    private $port = '5432';
    private $connection;

    public function __construct() {
        try {
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->database}";
            $this->connection = new PDO(
                $dsn,
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $e) {
            die("Error de conexión a PostgreSQL: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->connection;
    }

    public function prepare($sql) {
        return $this->connection->prepare($sql);
    }

    public function lastInsertId($sequence = null) {
        return $this->connection->lastInsertId($sequence);
    }
}
?>
