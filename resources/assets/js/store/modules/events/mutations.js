export const setAll = (state, { response }) => {
    _.forEach(response.data, function (event) {
        state.events.push(event)
    })

    state.events_pagination = response
}

export const clear = (state) => {
    state.events = []
    state.events_pagination = null
}

export const update = (state, command) => {
    const commandKey = _.findKey(state.events, {
        id: command.id,
        event_type: command.event_type
    })

    if (!commandKey) {
        state.events.unshift(command)
        return
    }

    Vue.set(state.events, parseInt(commandKey), command)
}

export const add = (state, { response }) => {
    state.events.unshift(response)
}


export const updateDeployment = (state, deployment) => {

    const siteDeployment = _.find(state.events, { id: deployment.site_deployment.id })

    if (siteDeployment) {

        _.each(deployment.site_deployment, function (value, key) {
            if (key !== 'server_deployments') {
                siteDeployment[key] = value
            }
        })

        const serverDeployment = _.find(siteDeployment.server_deployments, { id: deployment.server_deployment.id })

        if (serverDeployment) {

            _.each(deployment.server_deployment, function (value, key) {
                serverDeployment[key] = value
            })

            if(deployment.deployment_event) {
                Vue.set(
                    serverDeployment.events,
                    parseInt(_.findKey(serverDeployment.events, {
                        id: deployment.deployment_event.id
                    })),
                    deployment.deployment_event
                )
            }

        }

    }
}