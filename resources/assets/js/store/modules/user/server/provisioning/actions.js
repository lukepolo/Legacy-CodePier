export const getCurrentStep = ({}, server) => {
    return Vue.request().get(
        Vue.action('Server\ServerProvisionStepsController@index', { server: server }),
        'user_server_provisioning/setCurrentStep'
    )
}

export const setVersion = ({ commit }, data) => {
    commit('setVersion', data.version)
}

export const retry = ({}, server) => {
    Vue.request().post(
        Vue.action('Server\ServerProvisionStepsController@store', { server: server }),
        'user_server_provisioning/setCurrentStep'
    ).then(() => {
        app.showSuccess('Retrying to provision the server')
    })
}
