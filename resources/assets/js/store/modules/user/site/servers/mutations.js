export const set = (state, { response }) => {

}

export const setAll = (state, { response, requestData }) => {
    Vue.set(state.servers, requestData.value, _.map(response, 'id'))
}

export const add = (state, { response, requestData }) => {

}

export const remove = (state, { requestData }) => {
    Vue.set(state, 'servers', _.reject(state.servers, { id: requestData.value }))
}