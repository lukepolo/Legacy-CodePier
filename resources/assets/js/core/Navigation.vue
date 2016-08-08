<template>
    <header>
        <div class="logo-container">
            <img src="/assets/img/kodi_h.svg" alt="CodePier">
        </div>

        <ul class="nav nav-left nav-piles">
            <li class="dropdown arrow">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                   aria-expanded="false">
                    <span class="icon-layers"></span> {{ currentPile.name }}
                </a>

                <ul class="dropdown-menu" aria-labelledby="drop1">
                    <template v-for="pile in user.piles">
                        <li>
                            <a href="#" :class="{ selected : currentPile.id == pile.id }"><span
                                    class="icon-layers"></span> {{ pile.name }}</a>
                        </li>
                    </template>
                </ul>
            </li>
        </ul>

        <ul class="nav navbar-right nav-right">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                   aria-expanded="true">
                    <span class="muted">Team:</span> {{ currentTeam }} <span class="icon-arrow-down"></span>
                </a>

                <ul class="dropdown-menu" aria-labelledby="drop2">
                    <li>
                        <span class="dropdown-heading">Change Team</span>
                    </li>
                    <li>
                        <a href="#" v-on:click="changeTeam()"
                           :class="{selected : user.current_team_id == null}">Private</a>
                    </li>
                    <template v-for="team in user.teams">
                        <li>
                            <a href="#" v-on:click="changeTeam(team.id)"
                               :class="{selected : user.current_team_id == team.id}">{{ team.name }}</a>
                        </li>
                    </template>
                </ul>
            </li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                   aria-expanded="false">
                    <span class="icon-settings"></span>
                </a>

                <ul class="dropdown-menu">
                    <li>
                        <router-link to="/profile"><span class="icon-person"></span> My Profile</router-link>
                    </li>
                    <li>
                        <a href="#"><span class="icon-people"></span> Manage Teams</a>
                    </li>
                    <li>
                        <a href="#"><span class="icon-layers"></span> My Piles</a>
                    </li>
                    <li>
                        <a href="#"><span class="icon-power"></span> Logout</a>
                    </li>
                </ul>
            </li>
        </ul>
    </header>
</template>

<script>
    export default {
        data() {
            return {
                user: user
            }
        },
        computed: {
            currentTeam: function () {
                var currentTeam = 'Private';
                var currentTeamID = this.user.current_team_id;

                $.each(user.teams, function (index, team) {
                    if (currentTeamID == team.id) {
                        currentTeam = team.name;
                    }
                });

                return currentTeam;
            },
            currentPile: function () {
                return this.getCookie('pile', {
                    'id': null,
                    'name': '-'
                });
            }
        },
        methods: {
            changeTeam: function (teamID) {
                var user = this.user;
                Vue.http.post('teams/switch/' + (teamID ? teamID : "")).then((response) => {
                    user.current_team_id = teamID;
                    this.getPiles();
                }, (errors) => {
                    alert(error);
                });
            },
            getPiles : function() {
                alert('need to get piles http request');
            }
        }
    }
</script>