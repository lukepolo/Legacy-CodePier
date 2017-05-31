export const getServers = (state, getters, rootState) => (siteId) => {
    const siteServers = state.servers[siteId]

    if (siteServers && siteServers.length) {
        return _.filter(rootState.user_servers.servers, (server) => {
            return _.find(siteServers, {
                id: server.id
            })
        })
    }
}
