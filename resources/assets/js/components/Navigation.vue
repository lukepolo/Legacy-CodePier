<template>
    <header>
        <notifications></notifications>
        <div class="logo-container">
            <router-link to="/">
                <img src="/assets/img/codepier_w.svg">
            </router-link>
        </div>

        <ul class="nav nav-left nav-piles">
            <drop-down :name="currentPile ? currentPile.name : '-'" icon="icon-layers">
                <li>
                    <span class="dropdown-heading">Change Pile</span>
                </li>
                <template v-for="pile in piles">
                    <li>
                        <a @click="changePile(pile.id)"
                           :class="{ selected : (currentPile && currentPile.id == pile.id) }">
                            <span class="icon-layers"></span>
                            {{ pile.name }}
                        </a>
                    </li>
                </template>
            </drop-down>
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


            <drop-down muted="Team" :name="currentTeam" v-if="teamsEnabled()">
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
            </drop-down>
           <drop-down icon="icon-settings">
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
           </drop-down>
        </ul>
    </header>
</template>

<script>

    import Notifications from './Notifications.vue';

    export default {
        components: {
            Notifications
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
                return this.$store.state.system.version
            },
            piles() {
                return this.$store.state.user_piles.piles;
            },
            currentPile() {
                if(this.user) {
                    return this.getPile(this.user.current_pile_id);
                }
            },
            currentTeam() {
                let currentTeam = this.$store.state.user.user.current_team

                if(currentTeam) {
                    return currentTeam.name
                }
                return 'Private'
            },
            user() {
                return this.$store.state.user.user;
            },
            teams() {
                return this.$store.state.user_teams.teams;
            }
        },
        methods: {
            toggleSearch() {
                this.search = !this.search
                this.$refs.search.focus();
            },
            logout() {
                this.$store.dispatch('auth/logout')
            },
            changeTeam: function (teamID) {
                this.$store.dispatch('changeTeams', teamID);
            },
            changePile: function (pile_id) {
                this.$store.dispatch('user_piles/change', pile_id);
            }
        },
        created() {
            this.$store.dispatch('user_piles/get');
            this.$store.dispatch('user_teams/get');
        }
    }
</script>