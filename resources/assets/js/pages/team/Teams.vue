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
                <input v-model="create_form.name" name="name" type="text">

                <template v-for="pile in user_piles">
                    <input v-model="create_form.piles" name="piles[]" type="checkbox" :value="pile.id"> {{ pile.name }}
                </template>

                <button type="submit">Create Team</button>
            </form>

            <form @submit.prevent="updateTeam()" v-if="updating_team">
                Team Name :
                <input v-model="edit_form.name" name="name" type="text">

                <template v-for="pile in user_piles">
                    <input v-model="edit_form.piles" name="piles[]" type="checkbox" :value="pile.id"> {{ pile.name }}
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
        created() {
            this.fetchData();
        },
        data() {
            return {
                updating_team: false,
                creating_team: false,
                create_form : {
                    piles : [],
                    name : null

                },
                edit_form : {
                    piles : [],
                    name : null,
                    team : null
                }
            }
        },
        methods: {
            fetchData () {
                this.$store.dispatch('getUserPiles');
            },
            createTeam: function () {
                this.$store.dispatch('createTeam', this.create_form).then(() => {
                    this.create_form = this.$options.data().create_form;
                });
            },
            updateTeam: function () {
                this.$store.dispatch('updateTeam', this.edit_form).then(() => {
                    this.edit_form = this.$options.data().edit_form;
                });
            },
            deleteTeam: function (team_id) {
                this.$store.dispatch('deleteTeam', team_id);
            },
            isOwnerOfTeam: function (team) {
                return team.owner_id == this.$store.state.userStoreuser.id;
            },
            createTeamForm() {
                this.create_form = this.$options.data().create_form;
                this.edit_form = this.$options.data().create_form;
                this.creating_team = true;
                this.updating_team = false;
            },
            editTeam: function (team) {

                this.updating_team = true;
                this.creating_team = false;

                this.create_form = this.$options.data().create_form;

                this.edit_form.team = team.id;
                this.edit_form.name = team.name;
                this.edit_form.piles = _.map(team.piles, 'id');
            }
        },
        computed: {
            teams: () => {
                return userTeamStore.state.teams;
            },
            user_piles: () => {
                return this.$store.state.pilesStore.state.user_piles;
            }
        }
    }
</script>