export default {
    state: {
        provisioned_servers: [],
        servers_current_provisioning_step: {}
    },
    actions: {

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
