Vue.directive('file-editor', {
    bind: function (element, params) {
        const editor = ace.edit(element)

        editor.$blockScrolling = Infinity
        editor.getSession().setMode('ace/mode/sh')
        editor.setOption('maxLines', 45)
    }
})