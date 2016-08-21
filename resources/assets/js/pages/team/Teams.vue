<template>
    <section>
        <section id="middle" class="section-column">
            <div class="section-content">
                <div class="container">
                    <div class="panel-heading clearfix">
                        Teams
                        <a class="pull-right btn btn-default btn-sm" href="#" @click.prevent="creating_team = !creating_team" v-if="!creating_team">
                            <i class="fa fa-plus"></i> Create team
                        </a>

                        <div class="pull-right btn btn-default btn-sm" v-if="creating_team">
                            <form @submit.prevent="createTeam()">
                                Team Name :
                                <input v-model="team_name" name="team_name" type="text">
                                <button type="submit">Create Team</button>
                            </form>
                        </div>
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
                                        <router-link :to="{ path : '/my/team/' + team.id + '/members' }" class="btn btn-sm btn-default"> <i class="fa fa-users"></i> Members</router-link>
                                        <section v-if="isOwnerOfTeam(team)">
                                            <a href="#" class="btn btn-sm btn-default">
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
    </section>
</template>

<script>
    export default {
        data() {
          return {
              team_name : null,
              creating_team : false
          }
        },
        methods : {
          createTeam : function() {
              userTeamStore.dispatch('createTeam', {
                 name : this.team_name
              });
          },
          isOwnerOfTeam : function(team) {
              return team.owner_id == userStore.state.user.id;
          }
        },
        computed : {
            teams : () => {
                return userTeamStore.state.teams;
            }
        }
    }
</script>