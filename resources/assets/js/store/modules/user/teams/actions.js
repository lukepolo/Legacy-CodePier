export const get = ({}) => {
    return Vue.request().get(
        Vue.action('User\Team\UserTeamController@index'),
        'setAll'
    )
}

export const show = ({}, team) => {
    return Vue.request().get(
        Vue.action('User\Team\UserTeamController@show', { team: team }),
        'set'
    )
}

export const store = ({}, data) => {
    return Vue.request(data).post(
        Vue.action('User\Team\UserTeamController@store'),
        'add'
    )
}

export const update = ({}, data) => {
    return Vue.request().patch(
        Vue.action('User\Team\UserTeamController@update', { team: data.team }),
        'update'
    )
}

export const destroy = ({}, data) => {
    return Vue.request(data).delete(
        Vue.action('User\Team\UserTeamController@destroy', {team: data.team}),
        'remove'
    )
}

export const changeTeams = ({}, team) => {
    return Vue.request().post(
        Vue.action('User\Team\UserTeamController@switchTeam', { id: (team || '') }),
        'setTeam'
    ).then(() => {
        dispatch('piles/get')
        dispatch('sites/get')
    })
}