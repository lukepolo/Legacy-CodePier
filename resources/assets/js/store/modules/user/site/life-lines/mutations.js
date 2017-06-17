export const setAll = (state, { response }) => {
    state.life_lines = response
}

export const add = (state, { response }) => {
    state.life_lines.push(response)
}

export const update = (state, { response }) => {
    Vue.set(state.life_lines,
        parseInt(_.findKey(state.life_lines, { id: response.id })),
        response
    )
}

export const remove = (state, { requestData }) => {
    Vue.set(state, 'life_lines', _.reject(state.life_lines, { id: requestData.life_line }))
}
