<template>
    <header>
        <div class="logo-container">
            <router-link to="/">
                <img src="/assets/img/kodi_h.svg" alt="CodePier">
            </router-link>
        </div>

        <ul class="nav nav-left nav-piles">
            <li class="dropdown arrow">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                   aria-expanded="false">
                    <span class="icon-layers"></span>
                    <template v-if="currentPile">
                        {{ currentPile.name }}
                    </template>
                    <template v-else>
                        -
                    </template>
                </a>

                <ul class="dropdown-menu" aria-labelledby="drop1">
                    <template v-for="pile in piles">
                        <li>
                            <a v-on:click="changePile(pile.id)"
                               :class="{ selected : (currentPile && currentPile.id == pile.id) }"><span
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
                    <span class="muted">Team:</span>
                    <template v-if="currentTeam">
                        {{ currentTeam.name }}
                    </template>
                    <template v-else>
                        Private
                    </template>
                    <span class="icon-arrow-down"></span>
                </a>

                <ul class="dropdown-menu" aria-labelledby="drop2">
                    <li>
                        <span class="dropdown-heading">Change Team</span>
                    </li>
                    <li>
                        <a href="#" v-on:click="changeTeam()"
                           :class="{selected : currentTeam == null}">Private</a>
                    </li>
                    <template v-for="team in teams">
                        <li>
                            <a href="#" v-on:click="changeTeam(team.id)"
                               :class="{selected : (currentTeam && currentTeam.id == team.id)}">{{ team.name }}</a>
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
                        <router-link to="/my-profile"><span class="icon-person"></span>My Profile</router-link>
                    </li>
                    <li>
                        <router-link to="/my/teams"><span class="icon-people"></span>Manage Teams</router-link>
                    </li>
                    <li>
                        <router-link to="/piles"><span class="icon-layers"></span>My Piles</router-link>
                    </li>
                    <li>
                        <a @click.prevent="logout()"><span class="icon-power"></span> Logout</a>
                    </li>
                </ul>
            </li>
        </ul>
    </header>
</template>

<script>
    export default {
        computed: {
            piles: () => {

                return pileStore.state.piles;
            },
            current_pile_id: function () {
                return pileStore.state.current_pile_id;
            },
            currentTeam: function () {
                return userTeamStore.state.currentTeam;
            },
            currentPile: function () {
                return pileStore.state.currentPile;
            },
            user: () => {
                return userStore.state.user;
            },
            teams : () => {
                return userTeamStore.state.teams;
            }
        },
        methods: {
            logout : function() {
                Vue.http.post(this.action('Auth\LoginController@logout')).then(function() {
                    window.location = '/';
                });
            },
            changeTeam: function (teamID) {
                userTeamStore.dispatch('changeTeams', teamID);
            },
            changePile: function (pile_id) {
                pileStore.dispatch('setCurrentPileID', pile_id);

                if(this.$route.path == '/') {
                    serverStore.dispatch('getServers');
                    siteStore.dispatch('getSites');
                } else {
                    this.$router.push('/');

                }
            }
        },
        created() {
            this.$store.dispatch('getPiles');
            this.$store.dispatch('getTeams');
            this.$store.dispatch('getUserTeam');
        }
    }
</script>