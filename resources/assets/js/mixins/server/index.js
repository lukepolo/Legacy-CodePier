export const getServer = function(serverId, attribute) {
    const server = _.find(this.$store.state.user_servers.servers, { id: parseInt(serverId) })
    if (server) {
        if (attribute) {
            return server[attribute]
        }
        return server
    }
    return {}
}

export const serverHasFeature = (server, feature) => {
    return _.get(server.server_features, feature, false)
}