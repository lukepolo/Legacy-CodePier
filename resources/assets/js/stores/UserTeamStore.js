import Vue from "vue/dist/vue";
import Vuex from "vuex";
import {action} from "./helpers";

Vue.use(Vuex);

const userTeamStore = new Vuex.Store({
    state: {
        teams : [],
        currentTeam : null
    },
    actions: {
        changeTeams: ({commit}, teamID) => {
            Vue.http.post(this.action('User\Team\UserTeamController@switchTeam', { team : (teamID ? '/'+teamID : "") })).then((response) => {
                userStore.state.user.current_team_id = teamID;
                pileStore.getPiles();
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
        }
    },
    mutations: {
        SET_CURRENT_TEAM : (state, team) => {
            state.currentTeam = team;
        }
    }
});

export default userTeamStore