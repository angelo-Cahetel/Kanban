$(document).ready(function() {
    let draggedItem = null;

    // drag and drop
    $('.task-card').on('dragstart', function(e) {
        draggedItem = this;
        $(this).addClass('is-draggin');
        e.originalEvent.dataTransfer.effectAllowed = 'move';
    });

    $('.task-card').on('dragend', function() {
        $(this).removeClass('is-draggin');
        draggedItem = null;
    });

    $('.kanban-column').on('dragover', function(e) {
        e.preventDefault();
        e.originalEvent.dataTransfer.dropEffect = 'move';
        $(this).addClass('drago-over');
    });
    $('.kanban-column').on('dragleave', function() {
        $(this).removeClass('drago-over');
    });

    $('.kanban-column').on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('drag-over');

        if (draggedItem) {
            const taskId = $(draggedItem).data('task-id');
            const oldStatus = $(draggedItem).data('status');
            const newStatus = $(this).data('status');

            if (oldStatus === newStatus)  {
                // se for solto na mesma coluna, só retorna
                return;
            }
            // junta o item arrastado na nova coluna
            $(this).find('.task-list').append(draggedItem);
            // atualiza o status da tarefa para o estado da coluna
            $(draggedItem).data('status', newStatus);

            // atualiza o status da tarefa no back via ajax
            $.ajax({
                url: '/kanban_app/public/index.php?action=updateTaskStatus',
                type: 'POST',
                data: {
                    tarefa_id: taskId,
                    new_status: newStatus
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        console.log('Status da tarefa atualizado com sucesso:', response.message);
                        if (response.data_inicio) {
                            $(draggedItem).find('.data-inicio').text(response.data_inicio);
                        }
                        if (response.data_fim) {
                            $(draggedItem).find('.data-fim').text(response.data_fim);
                        }
                    } else {
                        console.error('Erro ao atualizar status da tarefa:', response.message);
                        $(`[data-status="${oldStatus}"] .task-list`).append(draggedItem);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro na requisição AJAX:', error);
                    $(`[data-status="${oldStatus}"] .task-list`).append(draggedItem);
                }
            });
        }
    });

    // modal para adicionar e editar tarefas
    const taskModal = $('#taskModal');
    const modalTitle = $('#modalTitle');
    const taskForm = $('#taskForm');
    const modalTaskId = $('#modalTaskId');
    const titleInput = $('#titulo');
    const descriptionInput = $('#descricao');
    const prioritySelect = $('#prioridade');
    const statusSelect = $('#status');
    const statusField = $('#statusField');

    // exibir modal para adicionar nova tarefa
    $('#addTaskBtn').on('click', function() {
        modalTitle.text('Adicionar Nova Tarefa');
        taskForm.attr('action', '/kanban_app/public/index.php?action=createTask');
        modalTaskId.val('');
        titleInput.val('');
        descriptionInput.val('');
        prioritySelect.val('normal'); // define prioridade padrão
        statusField.hide();
        taskModal.css('display', 'flex');
    });

    // Exibir modal para editar tarefa existente
    $('.task-list').on('click', 'btn-edit-task', function() {
        const taskData = $(this).data('task');
        modalTitle.text('Editar Tarefa');
        taskForm.attr('action', '/kanban_app/public/index.php?action=updateTask');
        modalTaskId.val(taskData.tarefa_id);
        titleInput.val(taskData.titulo);
        descriptionInput.val(taskData.descricao);
        prioritySelect.val(taskData.prioridade);
        statusSelect.val(taskData.status); // define o status atual
        statusField.show(); // exibe o campo de status para editar
        taskModal.css('display', 'flex');
    });

    // Fechar modal
    $('.close-button').on('click', function() {
        taskModal.hide();
    });

    // fechar modal quando clicar fora do conteudo
    $(window).on('click', function(event) {
        if ($(event.target).is(taskModal)) {
            taskModal.hide();
        }
    });

    const editTaskData = JSON.parse('<?= $editTaskData ?? "null" ?>');

    if (editTaskData) {
        modalTitle.text('Editar Tarefa');
        taskForm.attr('action', '/kanban_app/public/index.php?action=updateTask');
        modalTaskId.val(editTaskData.tarefa_id);
        titleInput.val(editTaskData.titulo);
        descriptionInput.val(editTaskData.descricao);
        prioritySelect.val(editTaskData.prioridade);
        statusSelect.val(editTaskData.status);
        statusField.show();
        taskModal.css('display', 'flex');
    }
});