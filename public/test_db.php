<!-- Arquivo criado para testar a conexão com o banco de dados -->

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../src/config/database.php';

echo "Tentando conectar ao banco de dados...<br>";

$database = new Database();
$conn = $database->getConnection();

if ($conn) {
    echo "Conexão com o banco de dados estabelecida com sucesso!<br>";
    try {
        $stmt = $conn->query("SELECT 1+1 AS resultado");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Teste de query simples: " . $result['resultado'] . "<br>";
    } catch (PDOException $e) {
        echo "Erro ao executar query de teste: " . $e->getMessage() . "<br>";
    }
} else {
    echo "Falha na conexão com o banco de dados. Verifique suas credenciais e configurações.<br>";
}

echo "Fim do teste.<br>";
?>