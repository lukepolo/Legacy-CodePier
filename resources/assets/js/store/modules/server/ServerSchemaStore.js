export default {
    state: {
        server_schemas: []
    },
    actions: {
        getServerSchemas: ({ commit }, server) => {
            Vue.http.get(Vue.action('Server\ServerSchemaController@index', { server: server })).then((response) => {
                commit('SET_SERVER_SCHEMAS', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        createServerSchema: ({ commit }, data) => {
            return Vue.http.post(Vue.action('Server\ServerSchemaController@store', { server: data.server }), data).then((response) => {
                commit('ADD_SERVER_SCHEMA', response.data)
                return response.data
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        deleteServerSchema: ({ commit }, data) => {
            Vue.http.delete(Vue.action('Server\ServerSchemaController@destroy', {
                server: data.server,
                schema: data.schema
            })).then(() => {
                commit('REMOVE_SERVER_SCHEMA', data.schema)
            }, (errors) => {
                app.handleApiError(errors)
            })
        }
    },
    mutations: {
        ADD_SERVER_SCHEMA: (state, schema) => {
            state.server_schemas.push(schema)
        },
        REMOVE_SERVER_SCHEMA: (state, schema) => {
            Vue.set(state, 'server_schemas', _.reject(state.server_schemas, { id: schema }))
        },
        SET_SERVER_SCHEMAS: (state, serverSchemas) => {
            state.server_schemas = serverSchemas
        }
    }
}
