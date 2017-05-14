export const set = (state, {response, requestData}) => {

}

export const setAll = (state, {response}) => {
    state.files = response
}

export const add = (state, {response, requestData}) => {

}

export const update = (state, {response, requestData}) => {

}

export const remove = (state, {response, requestData}) => {

}

export const setEditableFrameworkFiles = (state, {response}) => {
    state.editable_framework_files = response
}
// UPDATE_SITE_FILE: (state, file) => {
//     Vue.set(state.site_files[_.findKey(state.site_files, { id: file.id })], 'unencrypted_content', file.unencrypted_content)
// },