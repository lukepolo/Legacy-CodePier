<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <h3 class="section-header primary">
                My Teams
            </h3>
            <div class="section-content">
                <div class="container">
                    <div class="panel-heading clearfix">
                        Teams
                        <a class="pull-right btn btn-default btn-sm" href="#"
                           @click.prevent="createTeamForm()" v-if="!creating_team">
                            <i class="fa fa-plus"></i> Create team
                        </a>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="team in teams">
                                <td>{{ team.name }}</td>
                                <td>
                                    <span class="label label-success" v-if="isOwnerOfTeam(team)">Owner</span>
                                    <span class="label label-primary" v-else>Member</span>
                                </td>
                                <td>
                                    <router-link :to="{ path : '/my/team/' + team.id + '/members' }"
                                                 class="btn btn-sm btn-default"><i class="fa fa-users"></i> Members
                                    </router-link>

                                    <section v-if="isOwnerOfTeam(team)">
                                        <a @click.prevent="editTeam(team)" href="#" class="btn btn-sm btn-default">
                                            <i class="fa fa-pencil"></i> Edit
                                        </a>
                                    </section>

                                    <section v-if="isOwnerOfTeam(team)">
                                        <a @click.prevent="deleteTeam(team.id)" href="#" class="btn btn-sm btn-default">
                                            <i class="fa fa-trash-o"></i> Delete
                                        </a>
                                    </section>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
        <section id="right" v-if="creating_team || updating_team">
            <form @submit.prevent="createTeam()" v-if="creating_team">
                Team Name :
                <input v-model="team_name" name="team_name" type="text">

                <template v-for="pile in piles">
                    <input v-model="connected_piles" name="connected_piles[]" type="checkbox" :value="pile.id"> {{ pile.name }}
                </template>

                <button type="submit">Create Team</button>
            </form>

            <form @submit.prevent="updateTeam()" v-if="updating_team">
                Team Name :
                <input v-model="new_team_name" name="new_team_name" type="text">

                <template v-for="pile in piles">
                    <input v-model="updated_connected_piles" name="updated_connected_piles[]" type="checkbox" :value="pile.id"> {{ pile.name }}
                </template>

                <button type="submit">Update Team</button>
            </form>
        </section>
    </section>
</template>

<script>
    import LeftNav from './../../core/LeftNav.vue';
    export default {
        components: {
            LeftNav,
        },
        data() {
            return {
                team_name: null,
                new_team_name: null,
                connected_piles: [],
                updated_connected_piles: [],
                updating_team: false,
                creating_team: false,
                editing_team_id: null
            }
        },
        methods: {
            createTeam: function () {
                userTeamStore.dispatch('createTeam', {
                    name: this.team_name,
                    piles : this.connected_piles
                });
            },
            isOwnerOfTeam: function (team) {
                return team.owner_id == userStore.state.user.id;
            },
            deleteTeam: function (team_id) {
                userTeamStore.dispatch('deleteTeam', team_id);
            },
            updateTeam: function () {
                userTeamStore.dispatch('updateTeam', {
                    name: this.new_team_name,
                    team_id: this.editing_team_id,
                    piles : this.updated_connected_piles
                });
            },
            createTeamForm() {
                this.creating_team = true;
                this.updating_team = false;
            },
            editTeam: function (team) {
                this.editing_team_id = team.id;
                this.new_team_name = team.name;
                this.updated_connected_piles = _.map(team.piles, 'id');
                this.updating_team = true;
                this.creating_team = false;
            }
        },
        computed: {
            teams: () => {
                return userTeamStore.state.teams;
            },
            piles: () => {
                return pileStore.state.piles;
            }
        }
    }
</script>