$('.editor').each(function() {
    const editor = ace.edit(this);
    const form = $(this).closest('form');

    $(this).after('<input type="hidden" name="path" value="'+ $(this).data('path') +'">')
    $(this).after('<textarea class="hide" name="file">Loading . . .</textarea>');

    editor.getSession().setMode("ace/mode/sh");
    editor.setOption("maxLines", 45);

    editor.getSession().on('change', function(){
        form.find('textarea[name="file"]').val(editor.getSession().getValue());
    });

    $.get(laroute.action('Server\ServerController@getFile', {server : $(this).data('server_id'), path : $(this).data('path')}), function(envFile) {
        editor.getSession().setValue(envFile);
    });
});