<template>

    <section id="left" class="section-column" v-if="hasSites">



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

                <div class="site" v-for="site in sites">
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
import SiteForm from "./SiteForm.vue";
import Site from "./left-nav-components/Site.vue";

export default {
  components: {
    Site,
    SiteForm
  },
  methods: {
    changePile(pile_id) {
      this.$store.dispatch("user_piles/change", pile_id);
    }
  },
  computed: {
    userSshKeys() {
      return this.$store.state.user_ssh_keys.ssh_keys;
    },
    piles() {
      return this.$store.state.user_piles.piles;
    },
    currentPile() {
      if (this.user) {
        return this.getPile(this.user.current_pile_id);
      }
    },
    user() {
      return this.$store.state.user.user;
    },
    sites() {
      return _.filter(this.$store.state.user_sites.sites, site => {
        return site.pile_id === this.current_pile_id;
      });
    },
    current_pile_id() {
      return this.$store.state.user.user.current_pile_id;
    }
  }
};
</script>
