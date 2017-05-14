export const getDeployments = ({}) => {
    return Vue.request().get(
        Vue.action('User\UserController@getRunningDeployments'),
        'user_site_deployments/setDeployments'
    )
}

export const getDeploymentSteps = ({}, site) => {
    return Vue.request().get(
        Vue.action('Site\SiteDeploymentStepsController@getDeploymentSteps', { site : site }),
        'user_site_deployments/setDeploymentSteps'
    )
}

export const getSiteDeploymentSteps = ({}, site) => {
    return Vue.request().get(
        Vue.action('Site\SiteDeploymentStepsController@index', { site: site }),
        'user_site_deployments/setSiteDeploymentSteps'
    )
}

export const updateSiteDeployment = ({}, data) => {
    return Vue.request(data).post(
        Vue.action('Site\SiteDeploymentStepsController@store', { site: data.site })
    ).then(() => {
        app.showSuccess('You have updated the site deployment')
    })
}

// getDeployment : ({commit}, data) => {
//     return Vue.http.get(Vue.action('Site\SiteDeploymentsController@show', { site :  data.site, deployment: data.deployment })).then((response) => {
//         commit('ADD_NEW_SITE_DEPLOYMENT', response.data)
//         commit('UPDATE_SITE_DEPLOYMENT_STATUS', response.data)
//         return response.data
//     }, (errors) => {
//         app.handleApiError(errors)
//     })
// },
//
