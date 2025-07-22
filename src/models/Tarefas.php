<?php
class Task
{
    private $conn;
    private $table_name = "Tarefas";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getTasks($usuario_id, $tipo_usuario)
    {
        if ($tipo_usuario == 'GERENTE') {
            $query = "SELECT t.*, u.nome as nome_usuario
                      FROM " . $this->table_name . " t
                      JOIN usuarios u ON t.usuario_id = u.usuario_id
                      ORDER by t.data_criacao DESC";
            $stmt = $this->conn->prepare($query);
        } else {
            $query = "SELECT t.*, u.nome as nome_usuario
                      FROM " . $this->table_name . " t
                      JOIN usuarios u ON t.usuario_id = u.usuario_id
                      WHERE t.usuario_id = :usuario_id
                      ORDER by t.data_criacao DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario_id);
        }
        $stmt->execute();
        return $stmt; // retorna o statement para ser fetchado na controller
    }
    // propriedade para criar/atualizar
    public $tarefa_id;
    public $usuario_id;
    public $titulo;
    public $descricao;
    public $prioridade;
    public $status;
    public $data_criacao;
    public $data_inicio;
    public $data_fim;

    // método para criar uma nova tarefa
}
