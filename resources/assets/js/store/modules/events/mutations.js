export const setAll = (state, { response }) => {
    _.forEach(response.data, function (event) {
        state.events.push(event)
    })

    state.events_pagination = response
}

export const clear = (state) => {
    state.events = []
    state.events_pagination = null
}

export const update = (state, command) => {
    const commandKey = _.findKey(state.events, {
        id: command.id,
        event_type: command.event_type
    })

    if (!commandKey) {
        state.events.unshift(command)
        return
    }

    Vue.set(state.events, parseInt(commandKey), command)
}