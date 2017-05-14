document.onclick = (event) => {
    if((' ' + event.target.className + ' ').indexOf(' dropdown-toggle ') == -1) {
        app.$emit('close-dropdowns')
    }
}