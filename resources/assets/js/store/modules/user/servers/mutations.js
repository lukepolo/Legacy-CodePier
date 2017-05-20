export const set = (state, { response }) => {
    state.server = response
}

export const setAll = (state, { response }) => {
    state.servers = response
}

export const add = (state, { response }) => {
    state.servers.push(response)
}

export const update = (state, { response }) => {
    Vue.set(
        state.servers,
        parseInt(_.findKey(state.servers, { id: response.id })),
        response
    )
}

export const remove = (state, { requestData }) => {

    state.trashed.push(
        _.find(state.servers, {
            id: requestData.value }
        )
    )

    Vue.set(state, 'servers', _.reject(state.servers, {
        id: requestData.value
    }))
}

export const setTrashed = (state, { response }) => {
    state.trashed = response
}

export const removeFromTrash = (state, { requestData }) => {
    Vue.set(state, 'trashed', _.reject(state.trashed, {
        id: requestData.value
    }))
}
export const listenTo = (state, server) => {
    state.listening_to.push(server)
}
