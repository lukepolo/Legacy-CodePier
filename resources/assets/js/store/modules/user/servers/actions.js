export const get = ({ dispatch }) => {
    return Vue.request().get(
        Vue.action('Server\ServerController@index'),
        'user_servers/setAll'
    ).then((servers) => {
        _.each(servers, function (server) {
            dispatch('listenTo', server)
        })
    })
}

export const show = ({}, server) => {
    return Vue.request().get(
        Vue.action('Server\ServerController@show', { server: server }),
        'user_servers/set'
    )
}

export const store = ({ dispatch }, data) => {
    return Vue.request(data).post(
        Vue.action('Server\ServerController@store'),
        'user_servers/add'
    ).then((server) => {
        dispatch('listenTo', server)
        app.showSuccess('Your server is in queue to be provisioned')
        return server
    })
}

export const archive = ({}, server) => {
    return Vue.request(server).delete(
        Vue.action('Server\ServerController@destroy', { server: server }),
        [
            'user_servers/remove',
            'user_site_servers/remove'
        ]
    ).then(() => {
        if (app.$router.currentRoute.params.server) {
            app.$router.push('/')
        }
        app.showSuccess('You have archived the server')
    })

    // commit('REMOVE_SERVER_FROM_SITE_SERVERS', server)
}

export const getTrashed = ({}) => {
    return Vue.request().get(
        Vue.action('Server\ServerController@index', { trashed: true }),
        'user_servers/setTrashed'
    )
}

export const restore = ({}, server) => {
    return Vue.request(server).post(
        Vue.action('Server\ServerController@restore', { server: server }), [
            'user_servers/add',
            'user_servers/removeFromTrash'
        ]
    ).then(() => {
        //         dispatch('listenToServer', response.data)
    })
}

export const listenTo = ({ commit, state, dispatch }, server) => {
    if (_.indexOf(state.listening_to, server.id) === -1) {
        commit('listenTo', server)

        if (server.progress < 100) {
            dispatch('user_server_provisioning/getCurrentStep', server.id, { root: true })
        }

        Echo.private('App.Models.Server.Server.' + server.id)
            .listen('Server\\ServerProvisionStatusChanged', (data) => {
                // commit('UPDATE_SERVER', data.server)
                // commit('UPDATE_SITE_SERVER', data.server)
                // commit('SET_SERVERS_CURRENT_PROVISIONING_STEP', [data.server.id, data.serverCurrentProvisioningStep])
            })
            .listen('Server\\ServerSshConnectionFailed', (data) => {
                // commit('UPDATE_SERVER', data.server)
                // commit('UPDATE_SITE_SERVER', data.server)
            })
            .listen('Server\\ServerSshConnectionFailed', (data) => {

            })
            .listen('Server\\ServerCommandUpdated', (data) => {
                // commit('UPDATE_COMMAND', data.command)
                // commit('UPDATE_EVENT_COMMAND', data.command)
            })
            .notification((notification) => {
                switch (notification.type) {
                case 'App\\Notifications\\Server\\ServerMemory':
                case 'App\\Notifications\\Server\\ServerDiskUsage':
                case 'App\\Notifications\\Server\\ServerLoad':

                        // commit('SET_SERVER_STATS', {
                        //     server: server.id,
                        //     stats: notification.stats
                        // })
                    break
                }
            })
    }
}
