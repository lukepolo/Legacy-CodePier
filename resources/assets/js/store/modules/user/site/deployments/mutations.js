export const setDeployments = (state, { response }) => {
    state.deployments = response
}

export const setDeploymentSteps = (state, { response }) => {
    state.deployment_steps = response
}

export const setSiteDeploymentSteps = (state, { response }) => {
    state.site_deployment_steps = response
}

// UPDATE_RUNNING_SITE_DEPLOYMENT: (state, event) => {
//     if (!state.running_deployments[event.site_deployment.site_id]) {
//         Vue.set(state.running_deployments, event.site_deployment.site_id, [])
//     }
//
//     const siteDeployments = state.running_deployments[event.site_deployment.site_id]
//     const siteDeployment = siteDeployments[_.findKey(siteDeployments, { id: event.site_deployment.id })]
//
//     if (siteDeployment) {
//         _.each(event.site_deployment, function (value, key) {
//             if (key !== 'server_deployments') {
//                 siteDeployment[key] = value
//             }
//         })
//     } else {
//         siteDeployments.push(event.site_deployment)
//     }
// },
//     UPDATE_SITE_DEPLOYMENT_STATUS: (state, siteDeployment) => {
//
//     const siteKey = _.findKey(state.sites, { id: siteDeployment.site_id })
//
//     if (siteKey) {
//         Vue.set(state.sites[siteKey], 'last_deployment_status', siteDeployment.status)
//     }
// }
