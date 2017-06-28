export const get = () => {
    return Vue.request().get(
        Vue.action('UserTeamUserTeamController@index'),
        'user_teams/setAll',
    );
};

export const show = (context, team) => {
    return Vue.request().get(
        Vue.action('UserTeamUserTeamController@show', { team: team }),
        'user_teams/set',
    );
};

export const store = (context, data) => {
    return Vue.request(data).post(
        Vue.action('UserTeamUserTeamController@store'),
        'user_teams/add',
    );
};

export const update = (context, data) => {
    return Vue.request().patch(
        Vue.action('UserTeamUserTeamController@update', { team: data.team }),
        'user_teams/update',
    );
};

export const destroy = (context, data) => {
    return Vue.request(data).delete(
        Vue.action('UserTeamUserTeamController@destroy', { team: data.team }),
        'user_teams/remove',
    );
};

export const changeTeams = (context, team) => {
    return Vue.request()
        .post(
            Vue.action('UserTeamUserTeamController@switchTeam', {
                id: team || '',
            }),
            'user_teams/setTeam',
        )
        .then(() => {
            dispatch('user_piles/get');
        });
};
