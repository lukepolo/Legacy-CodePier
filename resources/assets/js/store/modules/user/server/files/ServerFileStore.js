export default {
    state: {
        server_files: [],
        server_editable_files: []
    },
    actions: {
        getServerFiles: ({ commit }, server) => {
            Vue.http.get(Vue.action('Server\ServerFileController@index', { server: server })).then((response) => {
                commit('SET_SERVER_FILES', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        addCustomFile: ({ commit }, server) => {
            Vue.http.get(Vue.action('Server\ServerFeatureController@getEditableFiles', { server: server })).then((response) => {
                commit('ADD_SERVER_FILE', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        getEditableServerFiles: ({ commit }, server) => {
            Vue.http.get(Vue.action('Server\ServerFeatureController@getEditableFiles', { server: server })).then((response) => {
                commit('SET_EDITABLE_SERVER_FILES', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        findServerFile: ({ commit }, data) => {
            return Vue.http.post(Vue.action('Server\ServerFileController@find', {
                server: data.server
            }), {
                file: data.file,
                custom: data.custom ? data.custom : false
            }).then((response) => {
                commit('ADD_SERVER_FILE', response.data)
                return response.data
            }, (errors) => {
                app.showError(errors)
            })
        },
        updateServerFile: ({}, data) => {
            Vue.http.put(Vue.action('Server\ServerFileController@update', {
                server: data.server,
                file: data.file_id
            }), {
                file_path: data.file,
                content: data.content
            }).then((response) => {
                app.showSuccess('You have updated the server file.')
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        reloadServerFile: ({ commit }, data) => {
            Vue.http.post(Vue.action('Server\ServerFileController@reloadFile', {
                file: data.file,
                server: data.server
            })).then((response) => {
                commit('UPDATE_SERVER_FILE', response.data)
                app.showSuccess('You have reloaded the server file.')
            }, (errors) => {
                app.handleApiError(errors)
            })
        }
    },
    mutations: {
        SET_SERVER_FILES: (state, files) => {
            state.server_files = files
        },
        ADD_SERVER_FILE: (state, file) => {
            state.server_files.push(file)
        },
        UPDATE_SERVER_FILE: (state, file) => {
            Vue.set(state.server_files[_.findKey(state.server_files, { id: file.id })], 'unencrypted_content', file.unencrypted_content)
        },
        SET_EDITABLE_SERVER_FILES: (state, files) => {
            state.server_editable_files = files
        }
    }
}
