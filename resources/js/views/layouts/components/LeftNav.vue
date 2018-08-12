<template>

    <section id="left" class="section-column" v-if="sites.length && currentPile">

        <drop-down tag="h3"  :name="currentPile ? `${currentPile.name} Sites` : '-'" icon="icon-layers" class="section-header pile-dropdown">
            <span slot="sub" class="icon-arrow-down"></span>
            <span slot="sub" class="icon-web"></span>

            <li>
                <span class="dropdown-heading">Change Pile</span>
            </li>
            <template v-for="pile in piles">
                <li>
                    <a @click="changePile(pile.id)"
                       :class="{ selected : (currentPile && currentPile.id === pile.id) }">
                        <span class="icon-layers"></span>

                        <template v-if="pile.name && pile.name.length > 18">
                            <tooltip :message="pile.name" placement="bottom">
                                <span  class="text-clip">{{ pile.name }}</span>
                            </tooltip>
                        </template>
                        <template v-else>
                            <span  class="text-clip">{{ pile.name }}</span>
                        </template>
                    </a>
                </li>
            </template>
        </drop-down>

        <div class="section-content">

            <div class="site-container">

                <div class="site" v-for="site in pileSites">
                    <site :site="site"></site>
                </div>

                <site-form :pile="currentPile.id"></site-form>
                
            </div>

            <div class="slack-invite" v-if="userSshKeys && !userSshKeys.length">
                <router-link :to="{ name : 'user_ssh_keys' }">
                    Create A SSH Key
                    <div class="small">You have not created an account ssh key</div>
                </router-link>
                <router-link :to="{ name : 'subscription' }" v-if="!isSubscribed">
                    Upgrade Account
                    <div class="small">Currently you only are getting 1 site and 1 server, upgrade now!</div>
                </router-link>
            </div>
        </div>

    </section>

</template>

<script>
import Site from "./left-nav-components/Site.vue";
import SiteForm from "./../../dashboard/components/SiteForm.vue";

export default {
  components: {
    Site,
    SiteForm,
  },
  methods: {
    changePile(pileId) {
      this.$store.dispatch("user/piles/changePile", pileId);
    },
  },
  computed: {
    sites() {
      return this.$store.state.user.sites.sites;
    },
    piles() {
      return this.$store.state.user.piles.piles;
    },
    pileSites() {
      return this.$store.getters["user/sites/sitesByPileId"](
        this.user.current_pile_id,
      );
    },
    userSshKeys() {
      return this.$store.state.user.ssh_keys.ssh_keys;
    },
    currentPile() {
      return this.$store.getters["user/piles/pileById"](
        this.user.current_pile_id,
      );
    },
  },
};
</script>
