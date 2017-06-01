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

export const show = (context, server) => {
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

export const archive = (context, server) => {
    return Vue.request(server).delete(
        Vue.action('Server\ServerController@destroy', { server: server }), [
            'user_servers/remove',
            'user_site_servers/remove'
        ]
    ).then(() => {
        if (app.$router.currentRoute.params.server_id) {
            app.$router.push('/')
        }
        app.showSuccess('You have archived the server')
    })
}

export const getTrashed = () => {
    return Vue.request().get(
        Vue.action('Server\ServerController@index', { trashed: true }),
        'user_servers/setTrashed'
    )
}

export const restore = ({ dispatch }, server) => {
    return Vue.request(server).post(
        Vue.action('Server\ServerController@restore', { server: server }), [
            'user_servers/add',
            'user_servers/removeFromTrash'
        ]
    ).then((server) => {
        dispatch('listenTo', server)
        return server
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
                commit('user_servers/update', {
                    response: data.server
                }, {
                    root: true
                })

                commit('user_site_servers/update', {
                    response: data.server
                }, {
                    root: true
                })

                commit('user_server_provisioning/setCurrentStep',
                    data.serverCurrentProvisioningStep, {
                        root: true
                    })
            })
            .listen('Server\\ServerSshConnectionFailed', (data) => {
                commit('user_servers/update', {
                    response: data.server
                }, { root: true })
            })
            .listen('Server\\ServerFailedToCreate', (data) => {
                commit('user_servers/update', {
                    response: data.server
                }, { root: true })
            })
            .listen('Server\\ServerCommandUpdated', (data) => {
                commit('user_commands/update', data.command, { root: true })
                commit('events/update', data.command, { root: true })
            })
            .notification((notification) => {
                switch (notification.type) {
                    case 'App\\Notifications\\Server\\ServerMemory':
                    case 'App\\Notifications\\Server\\ServerDiskUsage':
                    case 'App\\Notifications\\Server\\ServerLoad':
                        commit('user_servers/updateStats', {
                            server: server.id,
                            stats: notification.stats
                        }, {
                            root: true
                        })
                        break
                }
            })
    }
}
