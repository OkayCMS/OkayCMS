$(document).on('click', '.fn_open_ai_generate_meta', function () {

    let that = $(this),
        field = that.data('ai_field'),
        entity = that.data('ai_entity'),
        name = that.closest('form').find('[name="name"]').val(),
        entityId = that.closest('form').find('[name="id"]').val(),
        outputElement = that.closest('form').find('[name="' + field + '"]'),
        previousValue = outputElement.val(),
        started = false,
        failed = false;

    let eventSource = createEventSource(field, entity, name, entityId, false);

    eventSource.addEventListener('message', function (e) {
        if (failed) {
            return;
        }
        if (!started) {
            started = true;
            outputElement.val('');
        }
        outputElement.val(outputElement.val() + e.data);
        outputElement.trigger('input').scrollTop(outputElement[0].scrollHeight);
    });

    eventSource.addEventListener('error', function (e) {
        failed = true;
        outputElement.val(previousValue);
        outputElement.trigger('input');
        if (e.data) {
            alert(e.data);
        }
        eventSource.close();
    });

    eventSource.addEventListener('stop', function () {
        eventSource.close();
    });

    eventSource.onerror = function () {
        if (failed || started) {
            eventSource.close();
            return;
        }
        failed = true;
        outputElement.val(previousValue);
        outputElement.trigger('input');
        alert('ChatGPT request failed. Please try again.');
        eventSource.close();
    };
});

function generateEditorMeta(editor, field, entity, name, entityId)
{
    let previousValue = editor.getContent(),
        started = false,
        failed = false,
        eventSource = createEventSource(field, entity, name, entityId, true);

    eventSource.addEventListener('message', function (e) {
        if (failed) {
            return;
        }
        if (!started) {
            started = true;
            editor.setContent('');
        }
        editor.insertContent(e.data);
    });

    eventSource.addEventListener('error', function (e) {
        failed = true;
        editor.setContent(previousValue);
        if (e.data) {
            alert(e.data);
        }
        eventSource.close();
    });

    eventSource.addEventListener('stop', function () {
        eventSource.close();
    });

    eventSource.onerror = function () {
        if (failed || started) {
            eventSource.close();
            return;
        }
        failed = true;
        editor.setContent(previousValue);
        alert('ChatGPT request failed. Please try again.');
        eventSource.close();
    };
}

function createEventSource(field, entity, name, entityId, format)
{
    const params = new URLSearchParams({
        controller: 'OpenAiAdmin',
        field: field,
        entity: entity,
        name: name,
        entityId: entityId,
        format: String(format),
    });

    return new EventSource('/backend/index.php?' + params.toString());
}
