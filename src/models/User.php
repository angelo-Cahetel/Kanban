<?php 
class User {
    private $conn;
    private $table_name = "usuarios";

    public $usuario_id;
    public $nome;
    public $email;
    public $senha;
    public $tipo_usuario;
    public $data_registro;

    public function __construct($db) {
        $this->conn = $db;
    }

    // registrar um novo usuário
    public function register() {
        // inserir um novo registro
        $query = "INSERT INTO " . $this->table_name . "
                  SET
                    nome = :nome,
                    email = :email,
                    senha = :senha,
                    tipo_usuario = :tipo_usuario";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->senha = htmlspecialchars(strip_tags($this->senha));
        $this->tipo_usuario = htmlspecialchars(strip_tags($this->tipo_usuario));

        // bind values
        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':email', $this->email);
        // hash da senha antes de salvar
        $password_hash = password_hash($this->senha, PASSWORD_BCRYPT);
        $stmt->bindParam(':senha', $password_hash);
        $stmt->bindParam(':tipo_usuario', $this->tipo_usuario);

        // executa query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // verifica se o email já existe
    public function emailExist() {
        $query = "SELECT usuario_id, nome, email, senha, tipo_usuario
                  FROM " . $this->table_name . "
                  WHERE email = ?
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $this->email = htmlspecialchars(strip_tags($this->email));
        $stmt->bindParam(1, $this->email);
        $stmt->execute();

        $num = $stmt->rowCount();

        if ($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->usuario_id = $row['usuario_id'];
            $this->nome = $row['nome'];
            $this->senha = $row['senha'];
            $this->tipo_usuario = $row['tipo_usuario'];

            return true; // email já existe
        }
        return false; // email não existe
    }

    //  verifica a senha usada no login
    public function checkPassword($password_to_check) {
        return password_verify($password_to_check, $this->senha);
    }
}
?>