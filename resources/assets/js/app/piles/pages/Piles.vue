<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column" v-if="sites.length > 0">
            <h3 class="section-header primary">My Piles</h3>
            <div class="section-content">
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
        </section>
        <section v-else>
            <div class="container">
                <h1>
                    Welcome to CodePier! Lets Create Your First Site, we will walk you through your first site!
                </h1>
                <div>
                   1. Site Setup
                    <small>
                        Setup all of your sites repository information, deployment steps, and its language. This will setup some defaults
                        based on your applications language / framework which you can easily change in the site managment area.
                    </small>
                </div>
                <div>
                    2. Provision Infrastructure
                    <small>
                        Based on your sites setup, we are able to take that and easily provide you with a single server or
                        a load balanced setup with a few clicks!
                    </small>
                </div>
                <div>
                    3. Deploy!
                    <small>
                        Finally, you get to deploy your application!
                    </small>
                </div>
                <router-link class="btn" :to="{ name : 'create_site' }">Get Started</router-link>
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
        },
        user() {
            return this.$store.state.user.user;
        },
        sites() {
            return this.$store.state.user_sites.sites;
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
        },
        markAnnouncementRead() {
            this.$store.dispatch("system/markAnnouncementRead");
        }
    }
};
</script>
