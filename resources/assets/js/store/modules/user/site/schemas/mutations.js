export const setAll = (state, {response}) => {
    state.schemas = response
}

export const add = (state, {response}) => {
    state.schemas.push(response)
}

export const remove = (state, {requestData}) => {
    Vue.set(state, 'schemas', _.reject(state.schemas, { id: requestData.schema }))
}