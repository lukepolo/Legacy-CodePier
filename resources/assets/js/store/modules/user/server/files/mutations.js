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

//
// SET_SERVER_FILES: (state, files) => {
//     state.server_files = files
// },
//     ADD_SERVER_FILE: (state, file) => {
//     state.server_files.push(file)
// },
//     UPDATE_SERVER_FILE: (state, file) => {
//     Vue.set(state.server_files[_.findKey(state.server_files, { id: file.id })], 'unencrypted_content', file.unencrypted_content)
// },
//     SET_EDITABLE_SERVER_FILES: (state, files) => {
//     state.server_editable_files = files
// }
