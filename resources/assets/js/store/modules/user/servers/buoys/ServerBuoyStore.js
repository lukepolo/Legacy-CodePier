export default {
    state: {
        server_buoys: [],
        all_server_buoys : null
    },
    actions: {
        getServerBuoys: ({ commit }, serverId) => {
            Vue.http.get(Vue.action('Server\ServerBuoyController@index', { server: serverId })).then((response) => {
                commit('SET_SERVER_BUOYS', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        deleteServerBuoy: ({ commit }, data) => {
            Vue.http.delete(Vue.action('Server\ServerBuoyController@destroy', {
                server: data.server,
                buoy: data.buoy
            })).then(() => {
                commit('REMOVE_SERVER_BUOY', data.buoy)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        allServerBuoys : ( { commit }) => {
            Vue.http.get(Vue.action('Server\ServerBuoyController@myServerBuoys')).then((response) => {
                commit('SET_ALL_SERVER_BUOYS', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        }
    },
    mutations: {
        REMOVE_SERVER_BUOY: (state, buoyId) => {
            Vue.set(state, 'server_buoys', _.reject(state.server_buoys, { id: buoyId }))
        },
        SET_SERVER_BUOYS: (state, serverBuoys) => {
            state.server_buoys = serverBuoys
        },
        SET_ALL_SERVER_BUOYS: (state, serverBuoys) => {
            state.all_server_buoys = serverBuoys
        },
    }
}
