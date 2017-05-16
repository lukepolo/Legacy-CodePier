export const setAll = (state, { response }) => {
    state.buoys = response
}

export const remove = (state, { requestData }) => {
    Vue.set(state, 'buoys', _.reject(state.buoys, { id: requestData.buoy }))
}
