<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Kanban de Tarefas</title>
    <link rel="stylesheet" href="/kanban_app/public/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .register-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .register-container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .register-form input[type="text"],
        .register-form input[type="email"],
        .register-form input[type="password"],
        .register-form select {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .register-form button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .register-form button:hover {
            background-color: #218838;
        }
        .login-link {
            margin-top: 15px;
            font-size: 0.9em;
        }
        .login-link a {
            color: #007bff;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
        .error-message {
            color: red;
            margin-bottom: 15px;
        }
        .success-message {
            color: green;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Cadastre-se</h2>
        <?php if (isset($error)): ?>
            <p class="error-message"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <p class="success-message"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <form action="/kanban_app/public/index.php?action=register" method="POST" class="register-form">
            <input type="text" name="nome" placeholder="Seu Nome" required value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">
            <input type="email" name="email" placeholder="Seu Email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            <input type="password" name="senha" placeholder="Senha" required>
            <input type="password" name="confirmar_senha" placeholder="Confirmar Senha" required>
            <select name="tipo_usuario">
                <option value="COMUM" <?= (isset($_POST['tipo_usuario']) && $_POST['tipo_usuario'] == 'COMUM') ? 'selected' : '' ?>>Usuário Comum</option>
                <option value="GERENTE" <?= (isset($_POST['tipo_usuario']) && $_POST['tipo_usuario'] == 'GERENTE') ? 'selected' : '' ?>>Gerente</option>
            </select>
            <button type="submit">Cadastrar</button>
        </form>
        <p class="login-link">Já tem uma conta? <a href="/../public/index.php?action=showLogin">Faça Login</a>.</p>
    </div>
</body>
</html>