export const get = ({}) => {
    return Vue.request().get(
        Vue.action('User\Team\UserTeamController@index'),
        'user_teams/setAll'
    )
}

export const show = ({}, team) => {
    return Vue.request().get(
        Vue.action('User\Team\UserTeamController@show', { team: team }),
        'user_teams/set'
    )
}

export const store = ({}, data) => {
    return Vue.request(data).post(
        Vue.action('User\Team\UserTeamController@store'),
        'user_teams/add'
    )
}

export const update = ({}, data) => {
    return Vue.request().patch(
        Vue.action('User\Team\UserTeamController@update', { team: data.team }),
        'user_teams/update'
    )
}

export const destroy = ({}, data) => {
    return Vue.request(data).delete(
        Vue.action('User\Team\UserTeamController@destroy', { team: data.team }),
        'user_teams/remove'
    )
}

export const changeTeams = ({}, team) => {
    return Vue.request().post(
        Vue.action('User\Team\UserTeamController@switchTeam', { id: (team || '') }),
        'user_teams/setTeam'
    ).then(() => {
        dispatch('user_piles/get')
        dispatch('user_sites/get')
    })
}
