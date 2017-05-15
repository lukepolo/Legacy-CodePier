export const setAll = (state, {response}) => {
    state.environment_variables = response
}

export const add = (state, {response}) => {
    state.environment_variables.push(response)
}

export const remove = (state, {requestData}) => {
    Vue.set(state, 'environment_variables', _.reject(state.environment_variables, { id: requestData.environment_variable }))
}