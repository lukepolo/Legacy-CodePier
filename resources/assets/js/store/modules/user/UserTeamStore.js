export default {
  state: {
    teams: [],
    team: null,
    team_members: [],
    currentTeam: null
  },
  actions: {
    getTeams: ({ commit }) => {
      Vue.http.get(Vue.action('User\Team\UserTeamController@index')).then((response) => {
        commit('SET_TEAMS', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    createTeam: ({ commit, dispatch }, data) => {
      Vue.http.post(Vue.action('User\Team\UserTeamController@store'), data).then((response) => {
        dispatch('getCurrentUser')
        dispatch('getTeams')
        dispatch('getPiles')
        dispatch('getSites')
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    updateTeam: ({ commit, dispatch }, data) => {
      Vue.http.put(Vue.action('User\Team\UserTeamController@update', { team: data.team }), data).then((response) => {
        dispatch('getCurrentUser')
        dispatch('getTeams')
        dispatch('getPiles')
        dispatch('getSites')
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    changeTeams: ({ commit, dispatch }, teamID) => {
      Vue.http.post(Vue.action('User\Team\UserTeamController@switchTeam', { id: (teamID ? teamID : '') })).then((response) => {
        dispatch('getPiles').then(function () {
          dispatch('getCurrentUser')
          dispatch('getPiles')
          dispatch('getSites')
        })
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    getTeam: ({ commit }, team_id) => {
      Vue.http.get(Vue.action('User\Team\UserTeamController@show', { team: team_id })).then((response) => {
        commit('SET_TEAM', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    deleteTeam: ({ commit, dispatch }, team_id) => {
      Vue.http.delete(Vue.action('User\Team\UserTeamController@destroy', { team: team_id })).then((response) => {
        dispatch('getCurrentUser')
        dispatch('getTeams')
        dispatch('getPiles')
        dispatch('getSites')
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    getTeamMembers: ({ commit }, team_id) => {
      Vue.http.get(Vue.action('User\Team\UserTeamMemberController@show', { team: team_id })).then((response) => {
        commit('SET_TEAM_MEMBERS', response.data)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    sendTeamInvite: ({ commit, dispatch }, data) => {
      Vue.http.post(Vue.action('User\Team\UserTeamMemberController@invite'), {
        team_id: data.team_id,
        email: data.email
      }).then((response) => {
        dispatch('getTeam', data.team_id)
      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    resendTeamInvite: ({ commit }, invite_id) => {
      Vue.http.post(Vue.action('User\Team\UserTeamMemberController@resendInvite', { invite_id: invite_id })).then((response) => {

      }, (errors) => {
        app.handleApiError(errors)
      })
    },
    deleteTeamMember: ({ commit, dispatch }, data) => {
      Vue.http.delete(Vue.action('User\Team\UserTeamMemberController@destroy', {
        member: data.member_id,
        team: data.team_id
      })).then((response) => {
        dispatch('getTeam', data.team_id)
      }, (errors) => {
        app.handleApiError(errors)
      })
    }
  },
  mutations: {
    SET_TEAM: (state, team) => {
      state.team = team
    },
    SET_TEAMS: (state, teams) => {
      state.teams = teams
    },
    SET_TEAM_MEMBERS: (state, team_members) => {
      state.team_members = team_members
    }
  }
}
