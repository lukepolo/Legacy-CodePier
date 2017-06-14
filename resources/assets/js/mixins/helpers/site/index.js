export const hasSites = function () {
    return this.$store.state.user_sites.sites.length > 0
}

export const getSite = function (siteId, attribute) {
    const site = _.find(this.$store.state.user_sites.sites, { id: parseInt(siteId) })
    if (site) {
        if (attribute) {
            return site[attribute]
        }
        return site
    }
    return {}
}

export const workFlowCompleted = function() {

    let site = this.$store.state.user_sites.site

    if(site && site.workflow) {
        let workflow = _.findKey(site.workflow, function(status) {
            return status === false
        })

        if(workflow) {
            return workflow
        }

        return true
    }

    return false
}