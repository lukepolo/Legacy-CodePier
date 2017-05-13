export const set = (state, {response}) => {
    state.team = response
}

export const setAll = (state, {response}) => {
    state.teams = response
}

export const add = (state, {response}) => {
    state.teams.push(response)
}

export const update = (state, {response}) => {
    Vue.set(state.teams,
        parseInt(
            _.findKey(state.teams, {
                id : response.id
            })
        ),
        response
    )
}

export const remove = (state, {requestData}) => {
    Vue.set(state, 'teams', _.reject(state.teams, {
        id : requestData.team
    }))
}