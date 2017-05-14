export const setAll = (state, {response}) => {
    state.providers = response
}

export const remove = (state, {requestData}) => {
    Vue.set(state, 'providers', _.reject(state.providers, { id: requestData.server_provider }))
}