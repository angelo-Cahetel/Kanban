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
    public function createTask() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET
                    usuario_id = :usuario_id,
                    titulo = :titulo,
                    descricao = :descricao,
                    prioridade = :prioridade";

        $stmt = $this->conn->prepare($query);

        $this->usuario_id = htmlspecialchars(strip_tags($this->usuario_id));
        $this->titulo = htmlspecialchars(strip_tags($this->titulo));
        $this->descricao = htmlspecialchars(strip_tags($this->descricao));
        $this->prioridade = htmlspecialchars(strip_tags($this->prioridade));

        $stmt->bindParam(':usuario_id', $this->usuario_id);
        $stmt->bindParam(':titulo', $this->titulo);
        $stmt->bindParam(':descricao', $this->descricao);
        $stmt->bindParam(':prioridade', $this->prioridade);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // método para obter uma tarefa por ID
    public function getTaskById($tarefa_id) {
        $query = "SELECT t.*, u.nome as nome_usuario
                  FROM " . $this->table_name . " t
                  JOIN usuarios u ON t.usuario_id = u.usuario_id
                  WHERE t.tarefa_id = ?
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $tarefa_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row; // retorna a tarefa como um array associativo ou falso
    }

    // método para atualizar uma tarefa
    public function updateTask() {
        $query = "UPDATE " . $this->table_name . "
                    SET
                        titulo = :titulo,
                        descricao = :descricao,
                        prioridade = :prioridade,
                        status = :status,
                  WHERE 
                    tarefa_id = :tarefa_id";

        $stmt = $this->conn->prepare($query);

        $this->titulo = htmlspecialchars(strip_tags($this->titulo));
        $this->descricao = htmlspecialchars(strip_tags($this->descricao));
        $this->prioridade = htmlspecialchars(strip_tags($this->prioridade));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->tarefa_id = htmlspecialchars(strip_tags($this->tarefa_id));

        $stmt->bindParam(':titulo', $this->titulo);
        $stmt->bindParam(':descricao', $this->descricao);
        $stmt->bindParam(':prioridade', $this->prioridade);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':tarefa_id', $this->tarefa_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // método para atualizar o status de uma tarefa (incluindo data de inicio e fim)
    public function updateTaskStatus($tarefa_id, $new_status, $data_inicio = null, $data_fim = null) {
        $query = "UPDATE " . $this->table_name . "
                  SET status = :new_status,";

        // adiciona o campo de data se forem passados
        if ($data_inicio !== null) {
            $query .= ", data_inicio = :data_inicio";
        }
        if ($data_fim !== null) {
            $query .= ", data_fim = :data_fim";
        }

        $query .= " WHERE tarefa_id = :tarefa_id";

        $stmt = $this->conn->prepare($query);

        $tarefa_id = htmlspecialchars(strip_tags($tarefa_id));
        $new_status = htmlspecialchars(strip_tags($new_status));

        $stmt->bindParam(':new_status', $new_status);
        $stmt->bindParam(':tarefa_id', $tarefa_id);

        if ($data_inicio !== null) {
            $stmt->bindParam(':data_inicio', $data_inicio);
        }
        if ($data_fim !== null) {
            $stmt->bindParam(':data_fim', $data_fim);
        }

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // método para excluir uma tarefa
    public function deleteTask($tarefa_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE tarefa_id = ?";
        $stmt = $this->conn->prepare($query);

        $tarefa_id = htmlspecialchars(strip_tags($tarefa_id));
        $stmt->bindParam(1, $tarefa_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
