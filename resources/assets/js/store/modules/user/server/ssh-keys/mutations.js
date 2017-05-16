export const setAll = (state, { response }) => {
    state.ssh_keys = response
}

export const add = (state, { response }) => {
    state.ssh_keys.push(response)
}

export const remove = (state, { requestData }) => {
    Vue.set(state, 'ssh_keys', _.reject(state.ssh_keys, { id: requestData.ssh_key }))
}
