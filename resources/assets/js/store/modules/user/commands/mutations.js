export const set = (state, { response, requestData }) => {

}

export const setAll = (state, { response }) => {
    state.running_commands = response
}

export const add = (state, { response, requestData }) => {

}

export const update = (state, command) => {

    const commandKey = _.findKey(state.running_commands[command.commandable_type], { id: parseInt(command.id) })

    if (commandKey) {
        Vue.set(state.running_commands[command.commandable_type], parseInt(commandKey), command)
    } else {

        if (!state.running_commands[command.commandable_type]) {
            Vue.set(state.running_commands, command.commandable_type, [])
        }

        state.running_commands[command.commandable_type].push(command)
    }

}

export const remove = (state, { response, requestData }) => {

}
