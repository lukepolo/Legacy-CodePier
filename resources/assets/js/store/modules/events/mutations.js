export const setAll = (state, {response}) => {
    _.forEach(response.data, function (event) {
        state.events.push(event)
    })

    state.events_pagination = response
}

export const clear = (state) => {
    state.events = []
    state.events_pagination = null
}