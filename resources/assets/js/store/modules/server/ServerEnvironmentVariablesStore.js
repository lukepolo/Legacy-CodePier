export default {
    state: {
        server_environment_variables: []
    },
    actions: {
        getServerEnvironmentVariables: ({ commit }, serverId) => {
            Vue.http.get(Vue.action('Server\ServerEnvironmentVariablesController@index', { server: serverId })).then((response) => {
                commit('SET_SERVER_ENVIRONMENT_VARIABLES', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        createServerEnvironmentVariable: ({ commit }, data) => {
            return Vue.http.post(Vue.action('Server\ServerEnvironmentVariablesController@store', { server: data.server }), data).then((response) => {
                commit('ADD_SERVER_ENVIRONMENT_VARIABLE', response.data)
                return response.data
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        deleteServerEnvironmentVariable: ({ commit }, data) => {
            Vue.http.delete(Vue.action('Server\ServerEnvironmentVariablesController@destroy', {
                server: data.server,
                environment_variable : data.environment_variable
            })).then(() => {
                commit('REMOVE_SERVER_ENVIRONMENT_VARIABLE', data.environment_variable)
            }, (errors) => {
                app.handleApiError(errors)
            })
        }
    },
    mutations: {
        ADD_SERVER_ENVIRONMENT_VARIABLE: (state, serverEnvironmentVariable) => {
            state.server_environment_variables.push(serverEnvironmentVariable)
        },
        REMOVE_SERVER_ENVIRONMENT_VARIABLE: (state, serverEnvironmentVariableId) => {
            Vue.set(state, 'server_environment_variables', _.reject(state.server_environment_variables, { id: serverEnvironmentVariableId }))
        },
        SET_SERVER_ENVIRONMENT_VARIABLES: (state, serverEnvironmentVariables) => {
            state.server_environment_variables = serverEnvironmentVariables
        }
    }
}
