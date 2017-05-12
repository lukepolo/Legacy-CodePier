export const isCommandRunning = (type, modelId) => {
    const commands = _.filter(this.$store.state.serversStore.running_commands[type], (command) => {
        return command.commandable_id === modelId && command.status !== 'Completed' && command.status !== 'Failed'
    })

    if (commands) {
        // we only need the first one, this allows us to not allow other changes
        // on the rest of the servers till they are all completed
        return commands[0]
    }
}