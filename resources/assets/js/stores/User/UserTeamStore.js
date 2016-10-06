import Vue from "vue/dist/vue";
import Vuex from "vuex";
import {action} from ".././helpers";

const userTeamStore = new Vuex.Store({
    state: {
        teams: [],
        team: null,
        team_members: [],
        currentTeam: null,
    },
    actions: {
        getTeams: ({commit}) => {
            Vue.http.get(action('User\Team\UserTeamController@index')).then((response) => {
                commit('SET_TEAMS', response.data);
            }, (errors) => {
                alert(error);
            });
        },
        createTeam: ({commit}, data) => {
            Vue.http.post(action('User\Team\UserTeamController@store'), data).then((response) => {
                userTeamStore.dispatch('getTeams');
            }, (errors) => {
                alert(error);
            });
        },
        updateTeam: ({commit}, data) => {
            Vue.http.put(action('User\Team\UserTeamController@update', {team: data.team}), data).then((response) => {
                userTeamStore.dispatch('getTeams');
                pileStore.dispatch('getPiles');
                siteStore.dispatch('getSites');
            }, (errors) => {
                alert(error);
            });
        },
        changeTeams: ({commit}, teamID) => {
            Vue.http.post(action('User\Team\UserTeamController@switchTeam', {id: (teamID ? teamID : "")})).then((response) => {
                commit('SET_CURRENT_TEAM', response.data);
                pileStore.dispatch('getPiles').then(function () {
                    serverStore.dispatch('getServers');
                });
            }, (errors) => {
                alert(error);
            });
        },
        getUserTeam: ({commit}) => {
            var currentTeamID = userStore.state.user.current_team_id;

            $.each(userStore.state.user.teams, (index, team) => {
                if (currentTeamID == team.id) {
                    commit('SET_CURRENT_TEAM', team);
                    return false;
                }
            });
        },
        getTeam: ({commit}, team_id) => {
            Vue.http.get(action('User\Team\UserTeamController@show', {team: team_id})).then((response) => {
                commit('SET_TEAM', response.data);
            }, (errors) => {
                alert(error);
            });
        },
        deleteTeam: ({commit}, team_id) => {
            Vue.http.delete(action('User\Team\UserTeamController@destroy', {team: team_id})).then((response) => {
                userTeamStore.dispatch('getTeams');
            }, (errors) => {
                alert(error);
            });
        },
        getTeamMembers: ({commit}, team_id) => {
            Vue.http.get(action('User\Team\UserTeamMemberController@show', {team: team_id})).then((response) => {
                commit('SET_TEAM_MEMBERS', response.data);
            }, (errors) => {
                alert(error);
            });
        },
        sendTeamInvite: ({commit}, data) => {
            Vue.http.post(action('User\Team\UserTeamMemberController@invite'), {
                team_id: data.team_id,
                email: data.email
            }).then((response) => {
                userTeamStore.dispatch('getTeam', data.team_id);
            }, (errors) => {
                alert(error);
            });
        },
        resendTeamInvite: ({commit}, invite_id) => {
            Vue.http.post(action('User\Team\UserTeamMemberController@resendInvite', {invite_id: invite_id})).then((response) => {

            }, (errors) => {
                alert(error);
            });
        },
        deleteTeamMember: ({commit}, data) => {
            Vue.http.delete(action('User\Team\UserTeamMemberController@destroy', {
                member: data.member_id,
                team: data.team_id
            })).then((response) => {
                userTeamStore.dispatch('getTeam', data.team_id);
            }, (errors) => {
                alert(error);
            });
        }
    },
    mutations: {
        SET_TEAM: (state, team) => {
            state.team = team;
        },
        SET_TEAMS: (state, teams) => {
            state.teams = teams;
        },
        SET_CURRENT_TEAM: (state, team) => {
            if (_.isEmpty(team)) {
                team = null;
            }
            state.currentTeam = team;
        },
        SET_TEAM_MEMBERS: (state, team_members) => {
            state.team_members = team_members;
        }
    }
});

export default userTeamStore