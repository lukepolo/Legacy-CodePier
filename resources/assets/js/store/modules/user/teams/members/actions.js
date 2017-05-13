export const get = ({}, team) => {
    return Vue.request().get(
        Vue.action('User\Team\UserTeamMemberController@show', { team: team }),
        'setAll'
    )
}

export const destroy = ({}, data) => {
    return Vue.request(data).delete(
        Vue.action('User\Team\UserTeamMemberController@destroy', {
            team: data.team,
            member: data.member,
        }),
        'remove'
    )
}

export const sendInvite = ({}, data) => {
    return Vue.request(data).post(
        Vue.action('User\Team\UserTeamMemberController@invite'),
        'add'
    )
}

export const resendInvite = ({}, data) => {
    return Vue.request(data).post(
        Vue.action('User\Team\UserTeamMemberController@resendInvite', { invite_id: data.invite })
    )
}