document.onclick = (event) => {
    let target = event.target
    if (
        !app.hasClass(target, [
            'fa',
            'btn',
            'icon-*',
            'dropdown-toggle',
            'dropdown-content'
        ]) &&
        !app.isTag(target, 'textarea') &&
        !app.isTag(target, 'input') &&
        !app.isTag(target, 'select') &&
        !app.isTag(target, 'option')
    ) {
        app.$emit('close-dropdowns')
    }
}
