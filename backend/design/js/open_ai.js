$(document).on('click', '.fn_open_ai_generate_meta', function () {

    let that = $(this),
        field = that.data('ai_field'),
        entity = that.data('ai_entity'),
        name = that.closest('form').find('[name="name"]').val(),
        entityId = that.closest('form').find('[name="id"]').val(),
        outputElement = that.closest('form').find('[name="' + field + '"]')

    outputElement.val('');

    let eventSource = createEventSource(field, entity, name, entityId, false);

    eventSource.addEventListener('message', function (e) {
        outputElement.val(outputElement.val() + e.data);
        outputElement.trigger('input').scrollTop(outputElement[0].scrollHeight);
    });

    eventSource.addEventListener('stop', function (e) {
        eventSource.close();
    })
});

function generateEditorMeta(editor, field, entity, name, entityId)
{
    let eventSource = createEventSource(field, entity, name, entityId, true);
    editor.setContent('');

    eventSource.addEventListener('message', function (e) {
        editor.insertContent(e.data);
    });

    eventSource.addEventListener('stop', function (e) {
        eventSource.close();
    })
}

function createEventSource(field, entity, name, entityId, format)
{
    return new EventSource('/backend/index.php?controller=OpenAiAdmin&field=' + field +
        '&entity=' + entity +
        '&name=' + name +
        '&entityId=' + entityId +
        '&format=' + format
    );
}