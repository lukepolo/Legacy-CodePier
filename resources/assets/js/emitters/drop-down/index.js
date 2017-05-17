document.onclick = (event) => {
    if (!app.hasClass(event.target, [
            'fa',
            'btn',
            'icon-*',
            'dropdown-toggle',
            'dropdown-content'
        ])
    ) {
        app.$emit('close-dropdowns')
    }
}
