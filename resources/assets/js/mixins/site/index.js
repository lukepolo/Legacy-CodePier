export const getSite = function(siteId, attribute) {
    const site = _.find(this.$store.state.user_sites.sites, { id: parseInt(siteId) })
    if (site) {
        if (attribute) {
            return site[attribute]
        }
        return site
    }
    return {}
}