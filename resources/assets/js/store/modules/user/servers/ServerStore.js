export default {
    state: {
        servers: [],
        server: null,
        all_servers: [],
        server_sites: [],
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
