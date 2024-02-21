
// var outputElement = $('input[name="meta_title"]');
// // outputElement.innerHTML += "<br><br>";
//
// // const cursor = '<div style="width: 3px; height: 1rem; background: black; display: inline-block; vertical-align: middle"></div>';
// const cursor = '|';
//
// $(document).on('click', '.fn_open_ai_generate_meta', function () {
//     var eventSource = new EventSource('/backend/index.php?controller=OpenAiAdmin');
//     outputElement.val('');
//     // var response = '';
//     eventSource.addEventListener('message', function (e) {
//         outputElement.val(removeCursor(outputElement.val(), cursor) + e.data + cursor);
//     });
//
//
//
//     eventSource.addEventListener('stop', function (e) {
//         eventSource.close();
//         outputElement.val(removeCursor(outputElement.val(), cursor));
//         // outputElement.innerHTML = removeCursor(outputElement.innerHTML, cursor)
//     })
//
// });

function generateEditorMeta(editor, field, entity, name, entityId)
{
    // var getAiAdditionalData;
    if (typeof getAiAdditionalData !== "undefined") {
        console.log(23423423423)
    }

    var eventSource = new EventSource('/backend/index.php?controller=OpenAiAdmin&field=' + field +
        '&entity=' + entity +
        '&name=' + name +
        '&entityId=' + entityId
    );
    // outputElement.val('');
    // var response = '';
    eventSource.addEventListener('message', function (e) {
        // editor.insertContent(removeCursor(editor.getContent(), cursor) + e.data + cursor);
        editor.insertContent( e.data);
        // console.log(editor.getContent())
        // outputElement.val(removeCursor(outputElement.val(), cursor) + e.data + cursor);
    });



    eventSource.addEventListener('stop', function (e) {
        eventSource.close();
        // editor.insertContent(removeCursor(editor.getContent(), cursor));
        // outputElement.innerHTML = removeCursor(outputElement.innerHTML, cursor)
    })
    // editor.insertContent('&nbsp;<strong>It\'s my buttons dfsdfsdf!</strong>&nbsp;');
}

function removeCursor(text, cursor) {
    if (text.indexOf(cursor) === text.length - cursor.length) {
        return text.substring(
            0, text.length - cursor.length
        );
    }
    return text;
}

// $(window).on("unload", function(e) {
//     eventSource.close();
// });
function generateEditorMetaFields(element)
{
    let field    = element.prop('name');
    let entityId = element.closest('form').find('[name="id"]').val();
    let name     = element.closest('form').find('[name="name"]').val();
    let entity   = element.data('ai_entity');
console.log(field, entityId, name, entity);
    var eventSource = new EventSource('/backend/index.php?controller=OpenAiAdmin&field=' + field +
        '&entity=' + entity +
        '&entityId=' + entityId +
        '&name=' + name
    );

    element.val('');

    eventSource.addEventListener('message', function (e) {
    element.insertContent( e.data );
});

    eventSource.addEventListener('stop', function (e) {
        eventSource.close();
    });
    /* eventSource.addEventListener('message', function (e) {
        element.val(removeCursor(element.val(), cursor) + e.data + cursor);
    });

    eventSource.addEventListener('stop', function (e) {
        eventSource.close();
        element.val(removeCursor(element.val(), cursor));
    }); */
}
