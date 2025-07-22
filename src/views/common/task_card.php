<?php ?>

<div class="task-card" draggable="true" data-task-id="<?= $task['tarefa_id'] ?>" data-status="<?= $task['status'] ?>">
    <div class="task-header priority- <?= strtolower($task['prioridade']) ?>">
        <h3><?= htmlspecialchars($task['titulo']) ?></h3>
        <span>Prioridade: <?= htmlspecialchars($task['prioridade']) ?></span>
    </div>
    <p><?= nl2br(htmlspecialchars($task['descricao'])) ?></p>
    <?php if ($_SESSION['user_type'] === 'GERENTE'): ?>
        <small>Criado por: <?= htmlspecialchars($task['nome_usuario']) ?></small>
    <?php endif; ?>
    <?php if ($task['data_inicio'] && $task['data_fim']): ?>
        <?php
        $inicio = new DateTime($task['data_inicio']);
        $fim = new DateTime($task['data_fim']);
        $intervalo = $fim->diff($inicio);
        $tempo_gasto = '';
        if ($intervalo->days > 0) {
            $tempo_gasto = $intervalo->format('%a dias, %h horas e %i minutos');
        } else {
            $tempo_gasto = $intervalo->format('%h horas e %i minutos');
        }
        ?>
        <small>Tempo total: <?= $tempo_gasto ?></small>
    <?php endif; ?>
    <div class="task-actions">
        <!-- botões de editar e excluir visiveis se tiver permissão -->
        <?php if ($_SESSION['user_id'] == $task['usuario_id']  || $_SESSION['user_type'] === 'GERENTE'): ?>
            <a href="edit_task.php?id=<?= $task['tarefa_id'] ?>" class="btn-edit">Editar</a>
            <a href="/kanban_app/public/index.php?action=deleteTask&id=<?= $task['tarefa_id'] ?>" class="btn-delete" onclick="return confirm('Você tem certeza que deseja excluir esta tarefa?')">Excluir</a>
        <?php endif; ?>
    </div>
</div>