<template>
    <header>
        <notification-area></notification-area>
        <div class="logo-container">
            <router-link to="/">
                <img src="/assets/img/codepier_w.svg">
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
                    <li>
                        <span class="dropdown-heading">Change Pile</span>
                    </li>
                    <template v-for="pile in piles">
                        <li>
                            <a @click="changePile(pile.id)"
                               :class="{ selected : (currentPile && currentPile.id == pile.id) }"><span
                                    class="icon-layers"></span> {{ pile.name }}</a>
                        </li>
                    </template>
                </ul>
            </li>
        </ul>

        <section v-if="current_version != version">
            <div>
                Hello, We've got a new version of CodePier ready for you. <a href="">Refresh now</a> to make it yours.
            </div>
        </section>

        <ul class="nav navbar-right nav-right">

            <template v-if="local()">
                <li class="search-container">
                    <div class="search-form" :class="{ open : search }">
                        <input ref='search' type="text" placeholder="search..." v-model="form.query">
                    </div>
                    <a @click="toggleSearch()"><span class="icon-search"></span></a>
                </li>
                <li>
                    <router-link :to="{ name: 'bitts_market_place' }"><span class="icon-bitts"></span> Bitts</router-link>
                </li>

                <li>
                    <router-link :to="{ name: 'buoy_market_place' }"><span class="icon-buoy"></span> Buoys</router-link>
                </li>
            </template>


            <li class="dropdown" v-if="teamsEnabled()">
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
                        <a href="#" @click="changeTeam()"
                           :class="{selected : currentTeam == null}"><span class="icon-person"></span> Private</a>
                    </li>
                    <template v-for="team in teams">
                        <li>
                            <a href="#" @click="changeTeam(team.id)"
                               :class="{selected : (currentTeam && currentTeam.id == team.id)}"><span class="icon-people"></span> {{ team.name }}</a>
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
                        <router-link :to="{ name: 'my_account' }"><span class="icon-person"></span>My Account</router-link>
                    </li>
                    <li v-if="teamsEnabled()">
                        <router-link :to="{ name: 'teams' }"><span class="icon-people"></span>My Teams</router-link>
                    </li>
                    <li>
                        <router-link :to="{ name: 'piles' }"><span class="icon-layers"></span>My Piles</router-link>
                    </li>
                    <li>
                        <router-link :to="{ name: 'servers' }"><span class="icon-server"></span>My Servers</router-link>
                    </li>
                    <template v-if="isAdmin()">
                        <br>
                        Admin
                        <li>
                            <router-link :to="{ name: 'categories' }"><span class="icon-server"></span>Manage Categories</router-link>
                        </li>
                    </template>
                    <li>
                        <a @click.prevent="logout()"><span class="icon-power"></span> Logout</a>
                    </li>
                </ul>
            </li>
        </ul>
    </header>
</template>

<script>
    import NotificationArea from './NotificationArea.vue';
    export default {
        components: {
            NotificationArea
        },
        data() {
            return {
                form : {
                    query : 'Sorry, its coming soon!'
                },
                search : false,
                current_version : Laravel.version
            }
        },
        computed: {
            version() {
                return this.$store.state.eventsStore.version
            },
            piles() {
                return this.$store.state.pilesStore.piles;
            },
            currentPile() {
                if(this.user) {
                    return this.getPile(this.user.current_pile_id);
                }
            },
            currentTeam() {
                return this.$store.state.userStore.user.current_team;
            },
            user() {
                return this.$store.state.userStore.user;
            },
            teams() {
                return this.$store.state.teamsStore.teams;
            }
        },
        methods: {
            toggleSearch() {
                this.search = !this.search
                this.$refs.search.focus();
            },
            logout() {
                Vue.http.post(this.action('Auth\LoginController@logout')).then((response) => {
                    window.location = '/';
                }, (errors) => {
                    window.location = '/';
                });

            },
            changeTeam: function (teamID) {
                this.$store.dispatch('changeTeams', teamID);
            },
            changePile: function (pile_id) {
                this.$store.dispatch('changePiles', pile_id);
            }
        },
        created() {
            this.$store.dispatch('getPiles');
            this.$store.dispatch('getTeams');
        }
    }
</script>