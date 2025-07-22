<?php
// inicia a sessão se ainda não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

class AuthController
{
    private $db;
    private $user;

    public function __construct()
    {
        error_log("DEBUG: AuthController __construct() iniciado.");
        $database = new Database();
        $this->db = $database->getConnection();

        if ($this->db === null) {
            $_SESSION['error_message'] = "Erro interno do servidor";
            header("Location: /kanban_app/public/index.php?action=showLogin&error=db_conn_fail");
            exit();
        }

        $this->user = new User($this->db);
        error_log("DEBUG: AuthController __construct() finalizado.");
    }

    // mostra o formulário de login ou redireciona se já estiver logado
    public function showLogin()
    {
        if (isset($_SESSION['user_id'])) {
            header("Location: /kanban_app/public/index.php?action=tasks"); // redireciona para o dashboard se já estiver logado
            exit();
        }
        include __DIR__ . '/../views/auth/login.php';
    }

    // processa o login
    public function login()
    {
        error_log("DEBUG: AuthController login() iniciado. método da requisição: " . $_SERVER['REQUEST_METHOD']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['senha'] ?? '';


            if (empty($email) || empty($password)) {
                $_SESSION['error_message'] = "Por favor, preencha todos os campos.";
                error_log("DEBUG: Login - Campos vazios.");
                header("Location: /kanban_app/public/index.php?action=showLogin");
                exit();
            }

            $user = $this->user->findByEmail($email);

            if ($user && password_verify($password, $user['senha'])) {
                // Autenticação bem-sucedida
                $_SESSION['user_id'] = $user['usuario_id'];
                $_SESSION['user_type'] = $user['tipo_usuario'];
                $_SESSION['user_name'] = $user['nome']; // Opcional, para exibição
                $_SESSION['success_message'] = "Login realizado com sucesso!";
                error_log("DEBUG: Login bem-sucedido para o usuário ID: " . $user['usuario_id']);
                header("Location: /kanban_app/public/index.php?action=tasks");
                exit();
            } else {
                // Credenciais inválidas
                $_SESSION['error_message'] = "Email ou senha inválidos.";
                error_log("DEBUG: Login - Email ou senha inválidos para: " . $email);
                header("Location: /kanban_app/public/index.php?action=showLogin");
                exit();
            }
        } else {
            error_log("DEBUG: Login - Requisição não é POST.");
            header("Location: /kanban_app/public/index.php?action=showLogin");
            exit();
        }
    }
    // mostra o formulário de registro
    public function showRegister()
    {
        include __DIR__ . '/../views/auth/register.php';
    }
    // processa o envio do formulário de registro
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->user->nome = $_POST['nome'] ?? '';
            $this->user->email = $_POST['email'] ?? '';
            $this->user->senha = $_POST['senha'] ?? '';
            $confirm_password = $_POST['confirmar_senha'] ?? '';
            $this->user->tipo_usuario = $_POST['tipo_usuario'] ?? 'COMUM'; // padrão é COMUM

            if (empty($this->user->nome) || empty($this->user->email) || empty($this->user->senha) || empty($confirm_password)) {
                $error = "Todos os campos são obrigatórios.";
            } elseif ($this->user->senha !== $confirm_password) {
                $error = "As senhas não coincidem.";
            } elseif ($this->user->emailExist()) {
                $error = "Este email já está cadastrado.";
            } else {
                if ($this->user->register()) {
                    $success = "Registro realizado com sucesso! Você pode fazer login agora.";
                    // redireciona para a página de login
                    header("Refresh: 2; url=/kanban_app/public/index.php?action=showLogin");
                    exit();
                } else {
                    $error = "Erro ao registrar usuário.";
                }
            }
            include __DIR__ . '/../views/auth/register.php'; // recarrega a página de registro
        } else {
            $this->showRegister(); // mostra o formulário de registro
        }
    }
    // Realiza o logout
    public function logout()
    {
        session_unset();
        session_destroy();
        header("Location: /kanban_app/public/index.php?action=showLogin"); // redireciona para a página inicial
        exit();
    }
}
