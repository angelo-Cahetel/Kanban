<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'kanban_db';
    private $username = 'root';
    private $password = '';
    public $conn;

    public function getConnection(): ?PDO {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            error_log("Erro de conexão com o banco de dados: " . $exception->getMessage());
            return null;
        }
        return $this->conn;
    }
}
