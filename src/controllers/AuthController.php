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
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    // mostra o formulário de login ou redireciona se já estiver logado
    public function showLogin()
    {
        if (isset($_SESSION['user_id'])) {
            header("Location: /index.php");
            exit();
        }
        include __DIR__ . '/../views/auth/login.php';
    }

    // processa o login
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->user->email = $_POST['email'] ?? '';
            $password = $_POST['senha'] ?? '';

            if ($this->user->emailExist()) {
                if ($this->user->checkPassword($password)) {
                    // login bem sucedido, cria as variáveis de sessão
                    $_SESSION['user_id'] = $this->user->usuario_id;
                    $_SESSION['user_name'] = $this->user->nome;
                    $_SESSION['user_email'] = $this->user->email;
                    $_SESSION['user_type'] = $this->user->tipo_usuario; // armazena o tipo de usuário

                    header("LocatioNn: /index.php"); // redireciona para o dashboard
                    exit();
                } else {
                    $error = "Senha incorreta.";
                }
            } else {
                $error = "Email não encontrado.";
            }
            include __DIR__ . '/../views/auth/login.php'; // recarrega a página de login
        } else {
            $this->showLogin(); // mostra o formulário de login
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
                    header("Refresh: 2; url=/public/index.php");
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
        session_unset(); // remove as variáveis de sessão
        session_destroy(); // destrói a sessão
        header("Location: /public/index.php"); // redireciona para a página inicial
        exit();
    }
}
