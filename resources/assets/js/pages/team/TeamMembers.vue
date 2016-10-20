<template>
    <section v-if="team">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading clearfix">
                    Members of team "{{ team.name }}"
                </div>
                <div class="panel-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th v-if="isOwnerOfTeam">Action</th>
                        </tr>
                        </thead>
                        <tr v-for="member in members">
                            <td>{{ member.name }} - {{ member.email }}</td>
                            <td v-if="isOwnerOfTeam && member.id != current_user.id">
                                <button @click="deleteMember(member.id)" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i> Delete</button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading clearfix">Pending invitations</div>
                <div class="panel-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>E-Mail</th>
                            <th v-if="isOwnerOfTeam">Action</th>
                        </tr>
                        </thead>
                        <tr v-for="invite in team.invites">
                            <td>{{ invite.email }}</td>
                            <td v-if="isOwnerOfTeam">
                                <a @click.prevent="resendInvite(invite.id)" href="#" class="btn btn-sm btn-default">
                                    <i class="fa fa-envelope-o"></i> Resend invite
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>


            <div class="panel panel-default" v-if="isOwnerOfTeam">
                <div class="panel-heading clearfix">Invite to team</div>
                <div class="panel-body">
                    <form @submit.prevent="sendInvite()" class="form-horizontal" method="post" action="#">
                        <div class="form-group">
                            <label class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input v-model="email" type="email" class="form-control" name="email">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-envelope-o"></i>Invite to Team
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods : {
            fetchData : function() {
                this.$store.dispatch('getTeam', this.$route.params.team_id);
                this.$store.dispatch('getTeamMembers', this.$route.params.team_id);
            },
            sendInvite : function() {
                this.$store.dispatch('sendTeamInvite', {
                    email : this.email,
                    team_id : userTeamStore.state.team.id
                })
            },
            resendInvite : function(invite_id) {
                this.$store.dispatch('resendTeamInvite', invite_id);
            },
            deleteMember : function(member_id) {
                this.$store.dispatch('deleteTeamMember', {
                    member_id: member_id,
                    team_id : userTeamStore.state.team.id
                });
            }
        },
        data() {
          return {
              email : null
          }
        },
        computed : {
            current_user : () => {
              return userStore.state.user;
            },
            team : () => {
                return userTeamStore.state.team;
            },
            members : () => {
                return userTeamStore.state.team_members;
            },
            isOwnerOfTeam : function() {
                return this.team.owner_id == userStore.state.user.id;
            }
        }
    }
</script>