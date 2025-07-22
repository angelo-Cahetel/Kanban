<?php
session_start();
// DEBUG
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/models/User.php';
require_once __DIR__ . '/../src/models/Task.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../src/controllers/TaskController.php';

$action = $_GET['action'] ?? 'showLogin';

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
    case 'tasks':
        $taskController->index();
        break;
    case 'createTask':
        $taskController->create();
        break;
    case 'showEditTask':
        $taskController->showEdit(); // redireciona para index e usa js para abrir o modal
        break;
    case 'updateTask':
        $taskController->update();
        break;
    case 'deleteTask':
        $taskController->delete();
        break;
    case 'updateTaskStatus': // requisição ajax do drag and drop
        $taskController->updateStatus();
        break;

        default:
        header("Location: /kanban_app/public/index.php?action=showLogin"); // redireciona para o login se ação inválida
    }
