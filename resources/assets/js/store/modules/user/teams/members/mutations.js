export const setAll = (state, { response }) => {
    state.members = response
}

export const add = (state, { response }) => {
    state.members.push(response)
}

export const remove = (state, { requestData }) => {
    Vue.set(state, 'members', _.reject(state.members, {
        id: requestData.member
    }))
}
