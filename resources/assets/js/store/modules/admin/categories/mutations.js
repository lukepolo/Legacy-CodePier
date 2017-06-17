export const set = (state, { response }) => {
    state.category = response
}

export const setAll = (state, { response }) => {
    state.categories = response
}

export const add = (state, { response }) => {
    state.categories.push(response)
}

export const remove = (state, { requestData }) => {
    Vue.set(state, 'categories', _.reject(state.categories, { id: requestData.value }))
}