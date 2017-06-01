export const back = () => {
    window.history.back()
}

export const action = (action, parameters) => {
    return laroute.action(action, parameters)
}

