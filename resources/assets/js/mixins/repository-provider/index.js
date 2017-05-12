export const getRepositoryProvider = (providerId, attribute) => {
    const provider = _.find(this.$store.state.userStore.repository_providers, { id: providerId })
    if (provider) {
        if (attribute) {
            return provider[attribute]
        }
        return provider
    }
    return {}
}