export const getRepositoryProvider = function(providerId, attribute) {
    const provider = _.find(this.$store.state.user.repository_providers, { id: providerId })
    if (provider) {
        if (attribute) {
            return provider[attribute]
        }
        return provider
    }
    return {}
}