export const setAll = (state, { response, requestData }) => {
    state.servers[requestData.value] = response
    state.servers = Object.assign({}, state.servers, _.cloneDeep(state.servers))
}

export const remove = (state, { requestData }) => {
    _.each(state.servers, (servers, site) => {
        Vue.set(state.servers, site, _.reject(state.servers[site], { id: requestData.value }))
    })
}

export const update = (state, { response }) => {
    _.each(state.servers, (servers, site) => {
        const serverKey = _.findKey(state.servers[site], { id: response.id })
        if (serverKey) {
            Vue.set(state.servers[site], serverKey, response)
        }
    })

    state.servers = Object.assign({}, state.servers, _.cloneDeep(state.servers))
}

