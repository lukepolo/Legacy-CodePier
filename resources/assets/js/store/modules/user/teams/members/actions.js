export const get = (context, team) => {
  return Vue.request().get(
    Vue.action('UserTeamUserTeamMemberController@show', { team: team }),
    'user_team_members/setAll'
  );
};

export const destroy = (context, data) => {
  return Vue.request(data).delete(
    Vue.action('UserTeamUserTeamMemberController@destroy', {
      team: data.team,
      member: data.member
    }),
    'user_team_members/remove'
  );
};

export const sendInvite = (context, data) => {
  return Vue.request(data).post(
    Vue.action('UserTeamUserTeamMemberController@invite'),
    'user_team_members/add'
  );
};

export const resendInvite = (context, data) => {
  return Vue.request(data).post(
    Vue.action('UserTeamUserTeamMemberController@resendInvite', {
      invite_id: data.invite
    })
  );
};
