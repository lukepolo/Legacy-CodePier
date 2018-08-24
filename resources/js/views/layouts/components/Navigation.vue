<template>
    <header>
        <notifications></notifications>
        <div class="logo-container">
            <router-link to="/">
                <img src="./../../../../img/CP_Logo_TX-onGray.svg">
            </router-link>
        </div>

        <ul class="nav navbar-right nav-right">

            <template v-if="isSubscribed">
                <!--<li class="search-container">-->
                <!--<div class="search-form" :class="{ open : search }">-->
                <!--<input ref='search' type="text" placeholder="search..." v-model="form.query">-->
                <!--</div>-->
                <!--<a @click="toggleSearch()"><span class="icon-search"></span></a>-->
                <!--</li>-->
                <li>
                    <router-link :to="{ name: 'bitts_market_place' }"><span class="icon-bitts"></span> Bitts</router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'buoy_market_place' }"><span class="icon-buoy"></span> Buoys</router-link>
                </li>
            </template>

            <drop-down muted="Team" :name="currentTeam" v-if="teamsEnabled">
                <li>
                    <span class="dropdown-heading">Change Team</span>
                </li>
                <li>
                    <a href="#" @click.prevent="changeTeam()"
                       :class="{selected : currentTeam === null}"><span class="icon-person"></span> Private</a>
                </li>
                <template v-for="team in teams">
                    <li>
                        <a href="#" @click.prevent="changeTeam(team.id)"
                           :class="{selected : (currentTeam && currentTeam.id === team.id)}"><span class="icon-people"></span> {{ team.name }}</a>
                    </li>
                </template>
            </drop-down>

            <drop-down icon="icon-help">
                <li>
                    <a @click.prevent href="/" id="getHelp"><span class="icon-chat"></span> Get Help</a>
                </li>
                <li><a target="_blank" href="/faq">FAQs</a></li>
                <li><a target="_blank" href="/all-features">All Features</a></li>
                <li><a target="_blank" href="/change-log">Change Log</a></li>
            </drop-down>

            <drop-down icon="icon-settings">
                <li>
                    <router-link :to="{ name: 'my_account' }"><span class="icon-person"></span>My Account</router-link>
                </li>
                <li v-if="teamsEnabled">
                    <router-link :to="{ name: 'teams' }"><span class="icon-people"></span>My Teams</router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'piles' }"><span class="icon-layers"></span>My Piles</router-link>
                </li>
                <li>
                    <router-link :to="{ name: 'servers' }"><span class="icon-server"></span>My Servers</router-link>
                </li>
                <template v-if="isAdmin">
                    <li class="nav-label"><span>Admin</span></li>
                    <li>
                        <a href="/horizon" target="_blank"><span class="icon-laravel"></span> Laravel Horizon</a>
                    </li>
                    <li>
                        <router-link :to="{ name: 'categories' }"><span class="icon-settings"></span>Manage Categories</router-link>
                    </li>
                </template>
                <li class="nav-label"></li>
                <li>
                    <a @click.prevent="logout()"><span class="icon-power"></span> Logout</a>
                </li>
            </drop-down>
        </ul>
    </header>
</template>

<script>
import Vue from "vue";
import Notifications from "./Notifications";

export default Vue.extend({
  components: {
    Notifications,
  },
  data() {
    return {
      form: this.createForm({
        query: "Sorry, its coming soon!",
      }),
      search: false,
    };
  },
  computed: {
    currentTeam() {
      const currentTeam = this.user.current_team;

      if (currentTeam) {
        return currentTeam.name;
      }
      return "Private";
    },
    teams() {
      // TODO
      return [];
      return this.$store.state.user_teams.teams;
    },
  },
  methods: {
    toggleSearch() {
      this.search = !this.search;
      this.$refs.search.focus();
    },
    logout() {
      this.$store.dispatch("auth/logout").then(() => {
        this.$router.push({
          name: "login",
        });
      });
    },
    changeTeam(teamID) {
      // TODO
      // this.$store.dispatch("changeTeams", teamID);
    },
  },
});
</script>
