export const getServers = (state, getters, rootState) => (siteId) => {

    let siteServerIds = state.servers[siteId];

    if(siteServerIds && _.keys(siteServerIds).length) {
        return _.filter(rootState.user_servers.servers, (server) => {
            return _.indexOf(siteServerIds, server.id) > -1
        })
    }
}