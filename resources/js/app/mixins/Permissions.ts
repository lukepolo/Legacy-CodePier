import Vue from "vue";

Vue.mixin({
  computed: {
    user() {
      return this.$store.state.auth.user;
    },
    isAdmin() {
      return this.user && this.user.role === "admin";
    },
    teamsEnabled() {
      return false;
    },
    isSubscribed() {
      if (this.isAdmin) {
        return true;
      }

      return this.user && this.user.is_subscribed;
    },
    apiEnabled() {
      if (this.isAdmin) {
        return true;
      }

      if (this.isSubscribed) {
        return this.user && this.user.subscription_plan.includes("captain");
      }
      return false;
    },
    siteActionsEnabled() {
      if (this.isAdmin) {
        return true;
      }

      if (this.isSubscribed) {
        return true;
      }
      return this.$store.state.user_sites.sites.length <= 1;
    },
    serverTypesEnabled() {
      if (this.isAdmin) {
        return true;
      }

      return this.isSubscribed;
    },
    serverActionsEnabled() {
      if (this.isAdmin) {
        return true;
      }

      if (!this.user && this.user.confirmed) {
        return false;
      }

      let numberOfServers = this.$store.state.user_servers.servers.length;

      // if (!this.isSubscribed) {
      //     return numberOfServers <= 1;
      // }
      //
      // if (this.$store.state.user.user.subscription_plan.includes("firstmate")) {
      //     return numberOfServers <= 30;
      // }

      return true;
    },
    siteCreateEnabled() {
      if (this.isAdmin) {
        return true;
      }

      if (this.isSubscribed) {
        return true;
      }
      // return this.$store.state.user_sites.sites.length < 1;
      return true;
    },
    serverCreateEnabled() {
      if (this.isAdmin) {
        return true;
      }

      if (!this.user.confirmed) {
        return false;
      }

      // let numberOfServers = this.$store.state.user_servers.servers.length;
      //
      // if (!this.isSubscribed) {
      //     return numberOfServers < 1;
      // }
      //
      // if (this.$store.state.user.user.subscription_plan.includes("firstmate")) {
      //     return numberOfServers < 30;
      // }

      return true;
    },
  },
});
