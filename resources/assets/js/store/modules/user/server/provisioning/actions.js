export const getCurrentStep = ({}, server) => {
    return Vue.request().get(
        Vue.action('Server\ServerProvisionStepsController@index', { server: server }),
        'user_server_provisioning/setCurrentStep'
    )
}

export const show = ({}, data) => {
    return Vue.request(data).get('')
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

export const setVersion = ({ commit }, data) => {
    commit('system/setVersion', data.version)
}

//     retryProvisioning: ({ commit }, server) => {
//     Vue.http.post(Vue.action('Server\ServerProvisionStepsController@store', { server: server })).then((response) => {
//         commit('SET_SERVERS_CURRENT_PROVISIONING_STEP', [server, response.data])
//         app.showSuccess('Retrying to provision the server')
//     }, (errors) => {
//         app.handleApiError(errors)
//     })
// },
//     getCustomServerLink: ({}, data) => {
//     return Vue.http.get(Vue.action('Server\ServerController@getCustomServerScriptUrl', {
//         server: data.server,
//         site: data.site
//     })).then((response) => {
//         return response.data
//     }, (errors) => {
//         app.handleApiError(errors)
//     })
// }

