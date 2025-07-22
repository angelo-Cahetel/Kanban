$(document).rady(function() {
    let draggedItem = null;

    // quando começa a ser arrastado
    $('.task-card').on('dragstart', function(e) {
        draggedItem = $(this);
        $(this).addClass('is-dragging');
        e.originalEvent.dataTransfer.effectAllowed = 'move';
    });

   $('.task-card').on('dragend', function() {
        $(this).removeClass('is-dragging');
        draggedItem = null;
   });

//    quando o item é arrastado para outra coluna
$('.kanban-column').on('dragover', function(e) {
    e.preventDefault(); // permite o drop
    e.originalEvent.dataTransfer.dropEffect = 'move';
    $(this).addClass('drag-over'); // feedback visual
});

// quando o item arrastado sai de uma coluna
$('.kanban-column').on('dragleave', function() {
    $(this).removeClass('drag-over');
});

// quando o item é solto em outra coluna
$('.kanban-column').on('drop', function(e) {
    e.preventDefault();
    $(this).removeClass('drag-over');

    if (draggedItem) {
        const taskId = $(draggedItem).data('task-id');
        const oldStatus = $(draggedItem).data('status');
        const newStatus = $(this).data('status');

        // evita mover para a mesma coluna
        if (oldStatus === newStatus) {
            $(this).append(draggedItem); // o item volta se não for movido
            return;
        }

        // adicionar o item à nova coluna
        $(this).find('.task-list').append(draggedItem);

        // atualizar o status da tarefa no back via AJAX
        $.ajax({
            url: 'update_task_status.php', // endpoint php para atualização de status
            type: 'POST',
            data: {
                task_id: taskId,
                status: newStatus
            },
            success: function(response) {
                $(draggedItem).data('status', newStatus);
            },
            error: function(xhr, status, error) {
                console.error('Erro ao atualizar status da tarefa:', error);
            }
        })
    }
})
});