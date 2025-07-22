<?php
session_start(); // inicia no ponto de entrada

require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/models/Usuarios.php';
require_once __DIR__ . '/../src/models/Tarefas.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../src/controllers/TaskController.php';

$action = $_GET['action'] ?? 'showLogin'; // ação padrão é mostrar o login

$authController = new AuthController();
$taskController = new TaskController();

switch ($action) {
    // rotas de autenticação
    case 'showLogin':
        $authController->showLogin();
        break;
    case 'login':
        $authController->login();
        break;
    case 'showRegister':
        $authController->showRegister();
        break;
    case 'register':
        $authController->register();
        break;
    case 'logout':
        $authController->logout();
        break;

    // rotas de tarefas
    case 'tasks': // exibe o quadro Kanban
        $taskController->index();
        break;
    case 'createTask':
        $taskController->create();
        break;
    case 'showEditTask':
        $taskController->showEdit(); // redireciona para index e usa JS para abrir o modal
        break;
    case 'updateTask':
        $taskController->updade();
        break;
    case 'deleteTask':
        $taskController->delete();
        break;
    case 'updateTaskStatus': // requisição AJAX do drag and drop
        $taskController->updateStatus();
        break;

        default:
        header("Location: /../public/index.php?action=showLogin"); // redireciona para o login se ação inválida
    }
