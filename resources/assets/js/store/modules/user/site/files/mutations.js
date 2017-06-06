export const setAll = (state, { response }) => {
    state.files = response
}

export const add = (state, { response }) => {
    state.files.push(response)
}

export const update = (state, { response }) => {
    Vue.set(state.files[_.findKey(state.files, { id: response.id })], 'unencrypted_content', response.unencrypted_content)
}

export const setEditableFiles = (state, { response }) => {
    state.editable_files = response
}

export const setEditableFrameworkFiles = (state, { response }) => {
    state.editable_framework_files = response
}
