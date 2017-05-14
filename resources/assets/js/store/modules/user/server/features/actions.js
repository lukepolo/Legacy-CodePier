export const get = ({}, data) => {
    return Vue.request(data).get('')
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

export const setVersion = ({commit}, data) => {
    commit('system/setVersion', data.version)
}

getServerAvailableFeatures: ({ commit }) => {
    Vue.http.get(Vue.action('Server\ServerFeatureController@getFeatures')).then((response) => {
        commit('SET_AVAILABLE_SERVER_FEATURES', response.data)
    }, (errors) => {
        app.handleApiError(errors)
    })
},


    getServerFeatures: ({ commit }, server) => {
    Vue.http.get(Vue.action('Server\ServerFeatureController@index', { server: server })).then((response) => {
        commit('SET_SERVER_INSTALLED_FEATURES', response.data)
    }, (errors) => {
        app.handleApiError(errors)
    })
},

    installFeature: ({}, data) => {
    Vue.http.post(Vue.action('Server\ServerFeatureController@store', { server: data.server }), {
        service: data.service,
        feature: data.feature,
        parameters: data.parameters
    }).then((response) => {
        app.showSuccess('You have queued a server feature install')
    }, (errors) => {
        app.handleApiError(errors)
    })
},