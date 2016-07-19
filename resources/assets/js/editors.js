$('.editor').each(function() {
    var editor = ace.edit(this);
    var form = $(this).closest('form');

    $(this).after('<input type="hidden" name="path" value="'+ $(this).data('path') +'">')
    $(this).after('<textarea class="hide" name="file">Loading . . .</textarea>');

    editor.getSession().setMode("ace/mode/sh");
    editor.setOption("maxLines", 45);

    editor.getSession().on('change', function(){
        form.find('textarea[name="file"]').val(editor.getSession().getValue());
    });

    $.get($(this).data('url') + '?path='  + $(this).data('path'), function(envFile) {
        editor.getSession().setValue(envFile);
    });
});