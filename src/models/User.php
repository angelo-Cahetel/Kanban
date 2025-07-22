<?php
// src/models/User.php

class User
{
    private $conn;
    private $table_name = "usuarios"; // Nome da sua tabela de usuários

    public $usuario_id;
    public $nome;
    public $email;
    public $senha;
    public $tipo_usuario;
    public $data_registro;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Método para registrar um novo usuário
    public function register()
    {
        // query para inserir o registro
        $query = "INSERT INTO " . $this->table_name . "
                  SET
                      nome = :nome,
                      email = :email,
                      senha = :senha,
                      tipo_usuario = :tipo_usuario";

        // preparar a query
        $stmt = $this->conn->prepare($query);

        // limpar os dados
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->senha = htmlspecialchars(strip_tags($this->senha));
        $this->tipo_usuario = htmlspecialchars(strip_tags($this->tipo_usuario));

        // bind dos valores
        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':email', $this->email);

        // hash da senha antes de salvar no banco
        $password_hash = password_hash($this->senha, PASSWORD_BCRYPT);
        $stmt->bindParam(':senha', $password_hash);
        $stmt->bindParam(':tipo_usuario', $this->tipo_usuario);

        // executar a query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // encontrar usuario por e-mail
    public function findByEmail($email)
    {
        $query = "SELECT usuario_id, nome, email, senha, tipo_usuario, data_registro
                  FROM " . $this->table_name . "
                  WHERE email = :email
                  LIMIT 0,1"; // Limitar a 1 resultado

        $stmt = $this->conn->prepare($query);

        // bindar o valor do e-mail.
        $email = htmlspecialchars(strip_tags($email));
        $stmt->bindParam(':email', $email);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: false;
    }
    public function emailExist()
    {
        // Query para verificar se um e-mail existe
        $query = "SELECT usuario_id FROM " . $this->table_name . " WHERE email = :email LIMIT 0,1";

        $stmt = $this->conn->prepare($query);

        $this->email = htmlspecialchars(strip_tags($this->email));
        $stmt->bindParam(':email', $this->email);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return true;
        }

        return false;
    }
}
