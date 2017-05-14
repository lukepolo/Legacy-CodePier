export const get = ({}, data) => {

    let filters = (data && data.filters) ? data.filters : null

    filters = _.merge({
            page: data ? data.page : 1
        },
        _.omitBy({
            types: filters ? _.omitBy(filters.types, _.isEmpty) : null,
            piles: filters ? filters.piles : null,
            sites: filters ? filters.sites : null,
            servers: filters ? filters.servers : null
        }, _.isEmpty)
    )

    return Vue.request(filters).post(
        Vue.action('EventController@store'),
        'events/setAll'
    )
}

//
// ADD_NEW_SITE_DEPLOYMENT: (state, deployment) => {
//     state.events.unshift(deployment)
// },
//     // TODO - we need to add the type
//     UPDATE_DEPLOYMENT_EVENT: (state, event) => {
//     const siteDeployment = _.find(state.events, { id: event.site_deployment.id })
//
//     if (siteDeployment) {
//         const serverDeployment = _.find(siteDeployment.server_deployments, { id: event.server_deployment.id })
//         if (serverDeployment) {
//             Vue.set(
//                 serverDeployment.events,
//                 parseInt(_.findKey(serverDeployment.events, {
//                     id: event.deployment_event.id
//                 })),
//                 event.deployment_event
//             )
//         }
//     }
// },
//     UPDATE_SITE_DEPLOYMENT_EVENT: (state, event) => {
//
//     const siteDeploymentKey = _.findKey(state.events, { id: event.site_deployment.id })
//     const siteDeployment = state.events[siteDeploymentKey]
//
//     _.each(event.site_deployment, function (value, key) {
//         if (key !== 'server_deployments') {
//             siteDeployment[key] = value
//         }
//     })
//
// },
//     UPDATE_SERVER_DEPLOYMENT_EVENT: (state, event) => {
//
//     const siteDeployment = _.find(state.events, { id: event.site_deployment.id })
//
//     if (siteDeployment) {
//
//         const serverDeployment = _.find(siteDeployment.server_deployments, { id: event.server_deployment.id })
//
//         if (serverDeployment) {
//             _.each(event.server_deployment, function (value, key) {
//                 serverDeployment[key] = value
//             })
//         }
//     }
// },
//     UPDATE_EVENT_COMMAND: (state, command) => {
//     const commandKey = _.findKey(state.events, {
//         id: command.id,
//         event_type: command.event_type
//     })
//
//     if (!commandKey) {
//         state.events.unshift(command)
//         return
//     }
//
//     Vue.set(state.events, parseInt(commandKey), command)
// }