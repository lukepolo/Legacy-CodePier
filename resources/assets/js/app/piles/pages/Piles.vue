<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <h3 class="section-header primary">My Piles</h3>
            <div class="section-content">
                <div class="container">

                    <!--<template v-if="!hasSites">-->
                    <modal>
                        <div class="modal--header">
                            <h1>Welcome!</h1>
                        </div>
                        <div class="modal--body">
                            <p>
                                CodePier is designed to easily deploy sites based off its requirements. So we start off by
                                creating a site, setup its necessary requirements , attach a server, and then we can deploy and scale easily.
                            </p>

                            <h3>Getting Started</h3>
                            <div>
                                <p>Here are the basic steps to get you going :</p>
                                <ol>
                                    <li>Create your site in a pile</li>
                                    <li>Enter your repository details</li>
                                    <li>Follow the setup wizard to help you fill in your sites requirements</li>
                                    <li>Select the server type that suits your site</li>
                                    <li>Deploy your site</li>
                                </ol>
                            </div>

                            <h3>Getting Help</h3>
                            <p>
                                Click on the "<i class="fa fa-gear"></i>" icon, and select "Get Help", this will prompt you with a CodePier support <strong>developer</strong>, that's right a developer.
                            </p>

                            <p>Now get out there and start deploying!</p>
                        </div>

                        

                        <div class="modal--footer">
                            <span class="btn btn-primary">Get Started!</span>
                        </div>
                    </modal>
                    <!--</template>-->

                    <p class="info">
                        Piles are groupings for your sites. We've built defaults for you, but you can edit them to fit your needs.
                        To get started create a site.
                    </p>

                    <div class="group">
                        <pile v-on:deletePile="deletePile(index)" :pile="pile" :index="index" :key="pile.id" v-for="(pile, index) in piles"></pile>
                        <div class="group--item">
                            <a @click="newPile()" class="add-pile">
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
        </section>
    </section>
</template>

<script>
import { Pile } from "../components";

import LeftNav from "../../../components/LeftNav";

export default {
  components: {
    Pile,
    LeftNav
  },
  computed: {
    piles() {
      return this.$store.state.user_piles.piles;
    }
  },
  methods: {
    deletePile(index) {
      this.$store.commit("REMOVE_TEMP_PILE", index);
    },
    newPile() {
      this.$store.state.user_piles.piles.push({
        name: null,
        editing: true
      });
    }
  }
};
</script>
