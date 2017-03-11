export default {
    state: {
        servers: [],
        server: null,
        all_servers: [],
        server_sites: [],
        running_commands: {},
        trashed_servers: [],
        provisioned_servers: [],
        servers_listening_to: [],
        available_server_features: [],
        server_installed_features: [],
        available_server_languages: [],
        available_server_frameworks: [],
        servers_current_provisioning_step: {}
    },
    actions: {
        getServer: ({ commit }, server) => {
            Vue.http.get(Vue.action('Server\ServerController@show', { server: server })).then((response) => {
                commit('SET_SERVER', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        getServersCurrentProvisioningStep: ({ commit }, server) => {
            Vue.http.get(Vue.action('Server\ServerProvisionStepsController@index', { server: server })).then((response) => {
                commit('SET_SERVERS_CURRENT_PROVISIONING_STEP', [server, response.data])
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        retryProvisioning: ({ commit }, server) => {
            Vue.http.post(Vue.action('Server\ServerProvisionStepsController@store', { server: server })).then((response) => {
                commit('SET_SERVERS_CURRENT_PROVISIONING_STEP', [server, response.data])
                app.showSuccess('Retrying to provision the server')
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        getServers: ({ commit, rootState }) => {
            Vue.http.get(Vue.action('Server\ServerController@index', { pile_id: rootState.userStore.user.current_pile_id })).then((response) => {
                commit('SET_SERVERS', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        getAllServers: ({ commit, dispatch }) => {
            return Vue.http.get(Vue.action('Server\ServerController@index')).then((response) => {
                commit('SET_ALL_SERVERS', response.data)

                _.each(response.data, function (server) {
                    dispatch('listenToServer', server)
                })
                return response.data
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        getTrashedServers: ({ commit }) => {
            return Vue.http.get(Vue.action('Server\ServerController@index', { trashed: true })).then((response) => {
                commit('SET_TRASHED_SERVERS', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        listenToServer: ({ commit, state, dispatch }, server) => {
            if (_.indexOf(state.servers_listening_to, server.id) === -1) {
                commit('SET_SERVERS_LISTENING_TO', server)

                if (server.progress < 100) {
                    dispatch('getServersCurrentProvisioningStep', server.id)
                }

                Echo.private('App.Models.Server.Server.' + server.id)
                    .listen('Server\\ServerProvisionStatusChanged', (data) => {
                        commit('UPDATE_SERVER', data.server)
                        commit('UPDATE_SITE_SERVER', data.server)
                        commit('SET_SERVERS_CURRENT_PROVISIONING_STEP', [data.server.id, data.serverCurrentProvisioningStep])
                    })
                    .listen('Server\\ServerSshConnectionFailed', (data) => {
                        commit('UPDATE_SERVER', data.server)
                        commit('UPDATE_SITE_SERVER', data.server)
                    })
                    .listen('Server\\ServerSshConnectionFailed', (data) => {

                    })
                    .listen('Server\\ServerCommandUpdated', (data) => {
                        commit('UPDATE_COMMAND', data.command)
                        commit('UPDATE_EVENT_COMMAND', data.command)
                    })
                    .notification((notification) => {
                        switch (notification.type) {
                        case 'App\\Notifications\\Server\\ServerMemory':
                        case 'App\\Notifications\\Server\\ServerDiskUsage':
                        case 'App\\Notifications\\Server\\ServerLoad':

                            commit('SET_SERVER_STATS', {
                                server: server.id,
                                stats: notification.stats
                            })
                            break
                        }
                    })
            }
        },
        createServer: ({ commit, dispatch }, form) => {

            console.info(form)

            return Vue.http.post(Vue.action('Server\ServerController@store'), form).then((response) => {
                commit('ADD_SERVER', response.data)
                dispatch('listenToServer', response.data)
                app.showSuccess('Your server is in queue to be provisioned')

                return response.data
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        archiveServer: ({ commit }, server) => {
            Vue.http.delete(Vue.action('Server\ServerController@destroy', { server: server })).then(() => {
                if (app.$router.currentRoute.params.server) {
                    app.$router.push('/')
                }

                commit('REMOVE_SERVER', server)
                commit('REMOVE_SERVER_FROM_SITE_SERVERS', server)

                app.showSuccess('You have archived the server')

            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        restoreServer: ({ commit, dispatch }, server) => {
            Vue.http.post(Vue.action('Server\ServerController@restore', { server: server })).then((response) => {
                commit('ADD_SERVER', response.data)
                dispatch('listenToServer', response.data)
                commit('REMOVE_TRASHED_SERVER', response.data)
                app.showSuccess('You have restored the server')
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        getServerSites: ({ commit }, server) => {
            return Vue.http.get(Vue.action('Server\ServerSiteController@index', { server: server })).then((response) => {
                commit('SET_SERVER_SITES', response.data)
                return response.data
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        getServerAvailableFeatures: ({ commit }) => {
            Vue.http.get(Vue.action('Server\ServerFeatureController@getFeatures')).then((response) => {
                commit('SET_AVAILABLE_SERVER_FEATURES', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        getServerAvailableLanguages: ({ commit }) => {
            Vue.http.get(Vue.action('Server\ServerFeatureController@getLanguages')).then((response) => {
                commit('SET_AVAILABLE_SERVER_LANGUAGES', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        getServerAvailableFrameworks: ({ commit }) => {
            Vue.http.get(Vue.action('Server\ServerFeatureController@getFrameworks')).then((response) => {
                commit('SET_AVAILABLE_SERVER_FRAMEWORKS', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        installFeature: ({}, data) => {
            Vue.http.post(Vue.action('Server\ServerFeatureController@store', { server: data.server }), {
                service: data.service,
                feature: data.feature,
                parameters: data.parameters
            }).then((response) => {
                app.showSuccess('You have queued a server feature install')
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        getRunningCommands: ({ commit }) => {
            Vue.http.get(Vue.action('User\UserController@getRunningCommands')).then((response) => {
                commit('SET_RUNNING_COMMANDS', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        getServerFeatures: ({ commit }, server) => {
            Vue.http.get(Vue.action('Server\ServerFeatureController@index', { server: server })).then((response) => {
                commit('SET_SERVER_INSTALLED_FEATURES', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        getCustomServerLink: ({}, data) => {
            return Vue.http.get(Vue.action('Server\ServerController@getCustomServerScriptUrl', {
                server: data.server,
                site: data.site
            })).then((response) => {
                return response.data
            }, (errors) => {
                app.handleApiError(errors)
            })
        }
    },
    mutations: {
        ADD_SERVER: (state, server) => {
            state.servers.push(server)
            state.all_servers.push(server)
        },
        SET_SERVER: (state, server) => {
            state.server = server
        },
        SET_SERVERS: (state, servers) => {
            state.servers = servers
        },
        SET_SERVER_SITES: (state, serverSites) => {
            state.server_sites = serverSites
        },
        SET_AVAILABLE_SERVER_FEATURES: (state, availableServerFeatures) => {
            state.available_server_features = availableServerFeatures
        },
        SET_AVAILABLE_SERVER_LANGUAGES: (state, availableServerLanguages) => {
            state.available_server_languages = availableServerLanguages
        },
        SET_AVAILABLE_SERVER_FRAMEWORKS: (state, availableServerFrameworks) => {
            state.available_server_frameworks = availableServerFrameworks
        },
        SET_SERVERS_CURRENT_PROVISIONING_STEP: (state, [server, currentStep]) => {
            Vue.set(state.servers_current_provisioning_step, server, currentStep)
        },
        UPDATE_SERVER: (state, server) => {
            let foundServerKey = _.findKey(state.servers, { id: server.id })

            if (foundServerKey) {
                Vue.set(state.servers, foundServerKey, server)
            }

            foundServerKey = _.findKey(state.servers, { id: server.id })

            if (foundServerKey) {
                Vue.set(state.all_servers, foundServerKey, server)
            }
        },
        SET_ALL_SERVERS: (state, servers) => {
            state.all_servers = servers

            state.provisioned_servers = _.filter(servers, function (server) {
                if (server.status === 'Provisioned') {
                    return server
                }
            })
        },
        SET_SERVERS_LISTENING_TO: (state, server) => {
            state.servers_listening_to.push(server.id)
        },
        SET_RUNNING_COMMANDS: (state, commands) => {
            state.running_commands = Object.keys(commands).length > 0 ? commands : {}
        },
        UPDATE_COMMAND: (state, command) => {
            const commandKey = _.findKey(state.running_commands[command.commandable_type], { id: command.id })

            if (commandKey) {
                return Vue.set(state.running_commands[command.commandable_type], commandKey, command)
            }

            if (!state.running_commands[command.commandable_type]) {
                Vue.set(state.running_commands, command.commandable_type, [])
            }

            state.running_commands[command.commandable_type].push(command)
        },
        REMOVE_SERVER: (state, server) => {
            Vue.set(state, 'servers', _.reject(state.servers, { id: server }))
            Vue.set(state, 'all_servers', _.reject(state.all_servers, { id: server }))
        },
        SET_SERVER_INSTALLED_FEATURES: (state, serverFeatures) => {
            state.server_installed_features = serverFeatures
        },
        SET_TRASHED_SERVERS: (state, trashedServers) => {
            state.trashed_servers = trashedServers
        },
        REMOVE_TRASHED_SERVER: (state, server) => {
            Vue.set(state, 'trashed_servers', _.reject(state.trashed_servers, { id: server.id }))
        }
    }
}
