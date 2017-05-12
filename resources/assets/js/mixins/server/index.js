export const getServer = (serverId, attribute) => {
    const server = _.find(this.$store.state.serversStore.all_servers, { id: parseInt(serverId) })
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