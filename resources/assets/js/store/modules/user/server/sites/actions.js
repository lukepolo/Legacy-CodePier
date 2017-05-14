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

getServerSites: ({ commit }, server) => {
    return Vue.http.get(Vue.action('Server\ServerSiteController@index', { server: server })).then((response) => {
        commit('SET_SERVER_SITES', response.data)
        return response.data
    }, (errors) => {
        app.handleApiError(errors)
    })
},