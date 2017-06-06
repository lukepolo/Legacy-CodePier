export const setDeployments = (state, { response }) => {
    state.deployments = response
}

export const setDeploymentSteps = (state, { response }) => {
    state.deployment_steps = response
}

export const setSiteDeploymentSteps = (state, { response }) => {
    state.site_deployment_steps = response
}
