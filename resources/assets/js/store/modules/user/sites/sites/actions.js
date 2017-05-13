export const get = ({dispatch}) => {
    return Vue.request().get(
        Vue.action('Site\SiteController@index'),
        'user_sites/setAll'
    ).then((sites) => {
        _.each(sites, function (site) {
            console.info(site)
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

export const store = ({}, data) => {
    return Vue.request(data).post('')
}

export const update = ({}, data) => {
    return Vue.request(data).patch('')
}

export const destroy = ({}, data) => {
    return Vue.request(data).delete('')
}

export const listen = ({ commit, state, dispatch }, site) => {
    console.info('listen to ' + site.id)
    // if (_.indexOf(state.sites_listening_to, site.id) === -1) {
    //     commit('SET_SITES_LISTENING_TO', site)
    //     Echo.private('App.Models.Site.Site.' + site.id)
    //         .listen('Site\\DeploymentStepStarted', (data) => {
    //             commit('UPDATE_DEPLOYMENT_EVENT', data)
    //             commit('UPDATE_SERVER_DEPLOYMENT_EVENT', data)
    //             commit('UPDATE_SITE_DEPLOYMENT_EVENT', data)
    //             commit('UPDATE_RUNNING_SITE_DEPLOYMENT', data)
    //             commit('UPDATE_SITE_DEPLOYMENT_STATUS', data.site_deployment)
    //         })
    //         .listen('Site\\DeploymentStepCompleted', (data) => {
    //             commit('UPDATE_DEPLOYMENT_EVENT', data)
    //             commit('UPDATE_SERVER_DEPLOYMENT_EVENT', data)
    //             commit('UPDATE_SITE_DEPLOYMENT_EVENT', data)
    //             commit('UPDATE_RUNNING_SITE_DEPLOYMENT', data)
    //         })
    //         .listen('Site\\DeploymentStepFailed', (data) => {
    //             commit('UPDATE_DEPLOYMENT_EVENT', data)
    //             commit('UPDATE_SERVER_DEPLOYMENT_EVENT', data)
    //             commit('UPDATE_SITE_DEPLOYMENT_EVENT', data)
    //             commit('UPDATE_RUNNING_SITE_DEPLOYMENT', data)
    //             commit('UPDATE_SITE_DEPLOYMENT_STATUS', data.site_deployment)
    //         })
    //         .listen('Site\\DeploymentCompleted', (data) => {
    //             commit('UPDATE_SERVER_DEPLOYMENT_EVENT', data)
    //             commit('UPDATE_SITE_DEPLOYMENT_EVENT', data)
    //             commit('UPDATE_RUNNING_SITE_DEPLOYMENT', data)
    //             commit('UPDATE_SITE_DEPLOYMENT_STATUS', data.site_deployment)
    //         })
    //         .notification((notification) => {
    //             if (notification.type === 'App\\Notifications\\Site\\NewSiteDeployment') {
    //                 dispatch('getDeployment', {
    //                     site :  notification.siteDeployment.site_id,
    //                     deployment : notification.siteDeployment.id,
    //                 })
    //             }
    //         })
    // }
}
//     createSite: ({ commit, dispatch, rootState }, data) => {
//     return Vue.http.post(Vue.action('Site\SiteController@store'), {
//         domain: data.domain,
//         domainless: data.domainless,
//         pile_id: rootState.userStore.user.current_pile_id
//     }).then((response) => {
//         commit('ADD_SITE', response.data)
//         dispatch('listenToSite', response.data)
//         app.$router.push({ name: 'site_repository', params: { site_id: response.data.id }})
//         return response.data
//     }, (errors) => {
//         app.handleApiError(errors)
//     })
// },
//     updateSite: ({ commit }, data) => {
//     Vue.http.put(Vue.action('Site\SiteController@update', { site: data.site_id }), data.data).then((response) => {
//         commit('SET_SITE', response.data)
//         app.showSuccess('You have updated the site')
//     }, (errors) => {
//         app.handleApiError(errors)
//     })
// },
//     deleteSite: ({ commit, rootState }, siteId) => {
//     Vue.http.delete(Vue.action('Site\SiteController@destroy', { site: siteId })).then(() => {
//         commit('DELETE_SITE', siteId)
//         commit('REMOVE_SITE_FROM_PILE', {
//             site: siteId,
//             pile: rootState.userStore.user.current_pile_id
//         })
//         app.$router.push('/')
//         app.showSuccess('You have deleted the site')
//     }, (errors) => {
//         app.handleApiError(errors)
//     })
// },