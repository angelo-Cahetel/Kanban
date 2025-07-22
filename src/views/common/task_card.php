<div class="task-card" draggable="true" data-task-id="<?= $task['tarefa_od'] ?>" data-status="<?= $task['status'] ?>">
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
        $intervale = $fim->diff($inicio);
        $tempo_gasto = $intervalo->format('%d dias, %h horas e %i minutos');
        ?>
        <small>Tempo total: <?= $tempo_gasto ?></small>
    <?php endif; ?>
    <div class="task-actions">
        <!-- botões de editar e excluir visiveis se o usuário tiver permissão -->
        <?php if ($_SESSION['user_id'] == $task['usuario_id']  || $_SESSION['user_type'] === 'GERENTE'): ?>
            <a href="edit_task.php?id=<?= $task['tarefa_id'] ?>" class="btn-edit">Editar</a>
            <a href="delete_task.php?id=<?= $task['tarefa_id'] ?>" class="btn-delete" onclick="return confirm('Você tem certeza que deseja excluir esta tarefa?')">Excluir</a>
            <?php endif; ?>
    </div>
</div>