export const get = ({}, team) => {
    return Vue.request().get(
        Vue.action('User\Team\UserTeamMemberController@show', { team: team }),
        'user_team_members/setAll'
    )
}

export const destroy = ({}, data) => {
    return Vue.request(data).delete(
        Vue.action('User\Team\UserTeamMemberController@destroy', {
            team: data.team,
            member: data.member,
        }),
        'user_team_members/remove'
    )
}

export const sendInvite = ({}, data) => {
    return Vue.request(data).post(
        Vue.action('User\Team\UserTeamMemberController@invite'),
        'user_team_members/add'
    )
}

export const resendInvite = ({}, data) => {
    return Vue.request(data).post(
        Vue.action('User\Team\UserTeamMemberController@resendInvite', { invite_id: data.invite })
    )
}