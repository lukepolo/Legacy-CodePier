export const add = (state, notification) => {
    state.notifications.push(notification)
}

export const remove = (state, notification) => {
    state.notifications = _.without(state.notifications, notification)
}