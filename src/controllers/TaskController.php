<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models.Task.php';
require_once __DIR__ . '/../models/User.php'; // para exibir o nome do usuário na tarefa do gerente

class TaskController {
    private $db;
    private $task;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->task = new Task($this->db);
    }

    // protege as rotas, redirecionado se não estiver logado
    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            header("Lication: /../public/index.php");
            exit();
        }
    }

    // exibe o dashboard Kanban
    public function index() {
        $this->requireAuth(); // verifica se o usuário está logado

        $usuario_id = $_SESSION['user_id'];
        $tipo_usuario = $_SESSION['user_type'];

        // obtem todas as tarefas com base no tipo de usuário
        $stmt = $this->task->getTasks($usuario_id, $tipo_usuario);
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // organiza as tarefas por status para as colunas do Kanban
        $tasksByStatus = [
            'A_FAZER' => [],
            'Fazendo' => [],
            'REVISAO' => [],
            'CONCLUIDA' => []
        ];
        foreach ($tasks as $task) {
            $tasksByStatus[$task['status']][] = $task;
        }
        include __DIR__ . '/../views/tasks/index.php';
    }

    // exibe o formulário de criação de tarefa
    public function showCreate() {
        $this->requireAuth();

        header("Location: /../public/views/index.php");
        exit();
    }

    // cria uma nova tarefa
    public function create() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->task->usuario_id = $_SESSION['user_id'];
            $this->task->titulo = $_POST['titulo'] ?? '';
            $this->task->descricao = $_POST['descricao'] ?? '';
            $this->task->prioridade = $_POST['prioridade'] ?? 'MEDIA';

            if (empty($this->task->titulo)) {
                $_SESSION['error_message'] = "O título da tarefa é obrigatório.";
            } else {
                if ($this->task->createTask()) {
                    $_SESSION['success_message'] = "Tarefa criada com sucesso!";
                } else {
                    $_SESSION['error_message'] = "Erro ao criar a tarefa. Por favor, tente novamente.";
                }
            }
        }
        header("Location: /../public/views/index.php");
        exit();
    }

    // mostra o formulário de edição de tarefa
    public function showEdit() {
        $this->requireAuth();

        $task_id = $_GET['id'] ?? null;
        if (!$task_id) {
            $_SESSION['error_message'] = "ID da tarefa não fornecido.";
            header("Location: /../public/views/index.php");
            exit();
        }

        $task = $this->task->getTaskById($task_id);

        if (!$task) {
            $_SESSION['error_message'] = "Tarefa não encontrada.";
            header("Location: /../public/views/index.php");
            exit();
        }

        // verificação de permissão 
        if ($_SESSION['user_type'] !== 'GERENTE' && $_SESSION['user_id'] != $task['usuario_id']) {
            $_SESSION['error_message'] = "Você não tem permissão para editar esta tarefa.";
            header("Location: /../public/views/index.php");
            exit();
        }

        if ($this->task->deleteTask($task_id)) {
            $_SESSION['success_message'] = "Tarefa excluída com sucesso!";
        } else {
            $_SESSION['error_message'] = "Erro ao excluir a tarefa. Por favor, tente novamente.";
        }
        header("Location: /../public/views/index.php");
        exit();
    }

    // atualiza o status de uma tarefa via AJAX
    public function updateStatus() {
        $this->requireAuth(); // verifica se o usuário está logado

        header('Content-Type: application/json'); // resposta JSON

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id']) && isset($_POST['new_status'])) {
            $task_id = $_POST['task_id'];
            $new_status = $_POST['new_status'];

            $existingTask = $this->task->getTaskById($task_id);

            if (!$existingTask) {
                echo json_encode(['success' => false, 'message' => 'Tarefa não encontrada.']);
                exit();
            }

            //  verificação de permição
            if ($_SESSION['user_type'] !== 'GERENTE' && $_SESSION['user_id'] != $existingTask['usuario_id']) {
                echo json_encode(['success' => false, 'message' => 'Você não tem permissão para atualizar esta tarefa.']);
                exit();
            }

            // data de início e fim
            $current_status = $existingTask['status'];
            $data_inicio = $existingTask['data_inicio'];
            $data_fim = $existingTask['data_fim'];

            if ($new_status === 'EM_ANDAMENTO' && $data_inicio === null) {
                $data_inicio = date('Y-m-d H:i:s');
            } elseif ($new_status === 'CONCLUIDA' && $data_fim === null) {
                $data_fim = date('Y-m-d H:i:s');
            } elseif ($new_status !== 'CONCLUIDA' && $current_status === 'CONCLUIDA')  {
                $data_fim = null; // reseta a data de fim se a tarefa não estiver mais concluída
            }

            if ($this->task->updateTaskStatus($task_id, $new_status, $data_inicio, $data_fim)) {
                echo json_encode(['success' => true, 'message' => 'Status da tarefa atualizado com sucesso.', 'data_inicio' => $data_inicio, 'data_fim' => $data_fim]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar status da tarefa.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Requisição inválida.']);
        }
    }
}
?>