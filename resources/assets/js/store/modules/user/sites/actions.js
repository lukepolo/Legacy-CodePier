export const get = ({ dispatch }) => {
    return Vue.request().get(
        Vue.action('Site\SiteController@index'),
        'user_sites/setAll'
    ).then((sites) => {
        _.each(sites, function (site) {
            dispatch('listen', site)
        })
    })
}

export const show = ({}, site) => {
    return Vue.request().get(
        Vue.action('Site\SiteController@show', { site: site }),
        'user_sites/set'
    )
}

export const store = ({ dispatch }, data) => {
    return Vue.request(data).post(
        Vue.action('Site\SiteController@store'),
        'user_sites/add'
    ).then((site) => {
        dispatch('listen', site)
        app.$router.push({ name: 'site_repository', params: { site_id: site.id }})
    })
}

export const update = ({}, data) => {
    return Vue.request(data).patch(
        Vue.action('Site\SiteController@update', { site: data.site }), [
            'user_sites/set',
            'user_sites/update'
        ]
    ).then(() => {
        app.showSuccess('You have updated the site')
    })
}

export const destroy = ({}, site) => {
    return Vue.request(site).delete(
        Vue.action('Site\SiteController@destroy', { site: site }), [
            'user_sites/remove'
        ]
    ).then(() => {
        app.$router.push('/')
        app.showSuccess('You have deleted the site')
    })
}

export const listen = ({ commit, state }, site) => {
    if (_.indexOf(state.listening_to, site.id) === -1) {
        commit('listenTo', site)
        Echo.private('App.Models.Site.Site.' + site.id)
            .listen('Site\\DeploymentStepStarted', (data) => {
                commit('UPDATE_DEPLOYMENT_EVENT', data)
                commit('UPDATE_SERVER_DEPLOYMENT_EVENT', data)
                commit('UPDATE_SITE_DEPLOYMENT_EVENT', data)
                commit('UPDATE_RUNNING_SITE_DEPLOYMENT', data)
                commit('UPDATE_SITE_DEPLOYMENT_STATUS', data.site_deployment)
            })
            .listen('Site\\DeploymentStepCompleted', (data) => {
                commit('UPDATE_DEPLOYMENT_EVENT', data)
                commit('UPDATE_SERVER_DEPLOYMENT_EVENT', data)
                commit('UPDATE_SITE_DEPLOYMENT_EVENT', data)
                commit('UPDATE_RUNNING_SITE_DEPLOYMENT', data)
            })
            .listen('Site\\DeploymentStepFailed', (data) => {
                commit('UPDATE_DEPLOYMENT_EVENT', data)
                commit('UPDATE_SERVER_DEPLOYMENT_EVENT', data)
                commit('UPDATE_SITE_DEPLOYMENT_EVENT', data)
                commit('UPDATE_RUNNING_SITE_DEPLOYMENT', data)
                commit('UPDATE_SITE_DEPLOYMENT_STATUS', data.site_deployment)
            })
            .listen('Site\\DeploymentCompleted', (data) => {
                commit('UPDATE_SERVER_DEPLOYMENT_EVENT', data)
                commit('UPDATE_SITE_DEPLOYMENT_EVENT', data)
                commit('UPDATE_RUNNING_SITE_DEPLOYMENT', data)
                commit('UPDATE_SITE_DEPLOYMENT_STATUS', data.site_deployment)
            })
            .notification((notification) => {
                if (notification.type === 'App\\Notifications\\Site\\NewSiteDeployment') {
                    dispatch('getDeployment', {
                        site :  notification.siteDeployment.site_id,
                        deployment : notification.siteDeployment.id,
                    })
                }
            })
    }
}
