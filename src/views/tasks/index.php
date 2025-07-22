<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kanban de Tarefas</title>
    <link rel="stylesheet" href="/kanban_app/public/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/kanban_app/public/js/script.js" defer></script>
</head>

<body>
    <header>
        <h1>Bem-vindo ao Kanban, <?= htmlspecialchars($_SESSION['user_name']) ?> (<?= htmlspecialchars($_SESSION['user_type']) ?>)</h1>
        <nav>
            <button id="addTaskBtn">Adicionar Nova Tarefa</button>
            <a href="/kanban_app/public/index.php?action=logout" class="logout-btn">Sair</a>
        </nav>
    </header>

    <main class="kanban-board">
        <?php
        // mensagem de erro ou se der certo
        if (isset($_SESSION['success_message'])): ?>
            <div class="alert success">
                <?= htmlspecialchars($_SESSION['success_message']);
                unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert error">
                <?= htmlspecialchars($_SESSION['error_message']);
                unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <!-- Coluna A FAZER -->
        <div class="kanban-column" data-status="A_FAZER">
            <h2>A FAZER</h2>
            <div class="task-list">
                <?php foreach ($tasksByStatus['A_FAZER'] as $task): ?>
                    <?php include __DIR__ . '/../common/task_card.php'; // Reutiliza o card da tarefa 
                    ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Coluna EM ANDAMENTO -->
        <div class="kanban-column" data-status="EM_ANDAMENTO">
            <h2>EM ANDAMENTO</h2>
            <div class="task-list">
                <?php foreach ($tasksByStatus['EM_ANDAMENTO'] as $task): ?>
                    <?php include __DIR__ . '/../common/task_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Coluna REVISAO -->
        <div class="kanban-column" data-status="REVISAO">
            <h2>REVISÃO</h2>
            <div class="task-list">
                <?php foreach ($tasksByStatus['REVISAO'] as $task): ?>
                    <?php include __DIR__ . '/../common/task_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Coluna CONCLUIDA -->
        <div class="kanban-column" data-status="CONCLUIDA">
            <h2>CONCLUÍDA</h2>
            <div class="task-list">
                <?php foreach ($tasksByStatus['CONCLUIDA'] as $task): ?>
                    <?php include __DIR__ . '/../common/task_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <!-- Modal para Adicionar/Editar Tarefa -->
    <div id="taskModal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2 id="modalTitle">Adicionar Nova Tarefa</h2>
            <form id="taskForm" action="/kanban_app/public/index.php?action=createTask" method="POST">
                <input type="hidden" name="tarefa_id" id="modalTaskId">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" required>

                <label for="descricao">Descrição:</label>
                <textarea id="descricao" name="descricao"></textarea>

                <label for="prioridade">Prioridade:</label>
                <select id="prioridade" name="prioridade">
                    <option value="BAIXA">Baixa</option>
                    <option value="MEDIA">Média</option>
                    <option value="ALTA">Alta</option>
                    <option value="URGENTE">Urgente</option>
                </select>

                <!-- campo de status visivel apenas para edição -->
                <div id="statusField" style="display:none;">
                    <label for="status">Status:</label>
                    <select id="status" name="status">
                        <option value="A_FAZER">A Fazer</option>
                        <option value="EM_ANDAMENTO">EM_ANDAMENTO</option>
                        <option value="REVISAO">Revisão</option>
                        <option value="CONCLUIDA">Concluída</option>
                    </select>
                </div>

                <button type="submit">Salvar Tarefa</button>
            </form>
        </div>
    </div>

    <style>
        header {
            background-color: #333;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            margin: 0;
            font-size: 1.5em;
        }

        nav button,
        nav .logout-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            text-decoration: none;
            color: white;
        }

        #addTaskBtn {
            background-color: #28a745;
            margin-right: 10px;
        }

        #addTaskBtn:hover {
            background-color: #218838;
        }

        .logout-btn {
            background-color: #dc3545;
        }

        .logout-btn:hover {
            background-color: #c82333;
        }

        /* Estilos do Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            border-radius: 8px;
            width: 80%;
            max-width: 500px;
            position: relative;
        }

        .close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close-button:hover,
        .close-button:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-content form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .modal-content form input[type="text"],
        .modal-content form textarea,
        .modal-content form select {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .modal-content form button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .modal-content form button:hover {
            background-color: #0056b3;
        }

        /* Estilos para alertas */
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }

        .alert.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</body>

</html>