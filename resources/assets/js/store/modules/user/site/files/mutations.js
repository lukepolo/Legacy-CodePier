export const set = (state, { response, requestData }) => {

}

export const setAll = (state, { response }) => {
    state.files = response
}

export const add = (state, { response }) => {
    state.files.push(response)
}

export const update = (state, { response }) => {

}

export const setEditableFiles = (state, { response }) => {
    state.editable_files = response
}

export const setEditableFrameworkFiles = (state, { response }) => {
    state.editable_framework_files = response
}
// UPDATE_SITE_FILE: (state, file) => {
//     Vue.set(state.site_files[_.findKey(state.site_files, { id: file.id })], 'unencrypted_content', file.unencrypted_content)
// },
