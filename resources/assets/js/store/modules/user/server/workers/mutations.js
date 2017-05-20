export const setAll = (state, { response }) => {
    state.workers = response
}

export const add = (state, { response }) => {
    state.workers.push(response)
}

export const remove = (state, { requestData }) => {
    Vue.set(state, 'workers', _.reject(state.workers, { id: requestData.worker }))
}
