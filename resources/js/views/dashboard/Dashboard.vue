<template>
    <div id="middle" class="section-column">
        <h3 class="section-header primary">My Piles</h3>
        <div class="section-content">
            <div class="container">
                <modal v-if="showWelcomeModal">
                    <div class="modal--header">
                        <h1>Welcome!</h1>
                    </div>
                    <div class="modal--body">
                        <p>
                            CodePier is designed to customize your sever based off of your site's requirements. Because of this we recommend creating your site first,
                            set up it's necessary requirements, and finally create or attach a server.
                        </p>

                        <h3>Getting Started</h3>
                        <div>
                            <p>Here are the basic steps to get you going:</p>
                            <ol>
                                <li>Create a new site in a pile</li>
                                <li>Enter your repository details</li>
                                <li>Follow the setup wizard to help you fill in your site's requirements</li>
                                <li>Select the server type that you require</li>
                                <li>Deploy!</li>
                            </ol>
                        </div>

                        <h3>Getting Help</h3>
                        <p>
                            Click on the "<i class="icon-help"></i>" icon, and select "Get Help", this will connect you with a CodePier support <strong>developer</strong>. That's right, a developer.
                        </p>

                        <p>Now get out there and start deploying!</p>
                    </div>

                    <div class="modal--footer">
                        <span class="btn btn-primary" @click="markAnnouncementRead">Get Started!</span>
                    </div>
                </modal>

                <p class="info">
                    Piles are groupings for your sites. We've built defaults for you, but you can edit them to fit your needs.
                    To get started create a site.
                </p>

                <div class="group">
                    <pile :pile="pile" :key="pile.id" v-for="pile in piles"></pile>

                    <pile v-if="addingPile" @closeNewPile="addingPile = false"></pile>
                    <div class="group--item" v-else>
                        <a @click="addingPile = true" class="add-pile">
                            <div class="group--item-content">
                                <span class="icon-layers"></span>
                            </div>

                            <div class="btn-footer text-center">
                                <button class="btn btn-primary">Add Pile</button>
                            </div>
                        </a>
                    </div>

                    <div class="group--item" v-if="!isSubscribed">
                        <router-link :to="{ name : 'subscription' }" class="subscribe-pile">
                            Upgrade Account
                            <div class="small">The free plan only allows for 1 site. <br>Upgrade now to add more!</div>
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Pile from "./components/Pile";

export default {
  components: {
    Pile,
  },
  data() {
    return {
      addingPile: false,
    };
  },
  computed: {
    piles() {
      return this.$store.state.user.piles.piles;
    },
    showWelcomeModal() {
      return !this.user.last_read_announcement;
    },
  },
  methods: {
    markAnnouncementRead() {
      this.$store.dispatch("user/markAnnouncementRead");
    },
  },
};
</script>
