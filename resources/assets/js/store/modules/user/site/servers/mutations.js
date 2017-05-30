export const setAll = (state, { response, requestData }) => {
    state.servers[requestData.value] = _.map(response, 'id')
    state.servers = Object.assign({}, state.servers, _.cloneDeep(state.servers))
}

export const remove = (state, { requestData }) => {
    _.each(state.servers, (servers, site) => {
        Vue.set(state.servers, site, _.reject(state.servers[site], { id: requestData.value }))
    })

}
