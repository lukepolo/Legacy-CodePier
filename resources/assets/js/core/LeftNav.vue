<template>
    <section id="left" class="section-column">
        <h3 class="section-header">Sites</h3>
        <div class="server selected" v-for="site in sites">
            <router-link :to="{ path: '/site/'+site.id }">
                <span class="server-connection server-success" data-toggle="tooltip" data-placement="top" data-container="body" title="Site Health"></span> {{ site.domain }}
            </router-link>
        </div>

        <form @submit.prevent="saveSite" v-if="adding_site">
            Domain
            <input v-model="domain" type="text" :value="domain">
            <button class="btn btn-primary">Save</button>
        </form>

        <div class="section-content">
            <div class="server text-center">
                <div @click="adding_site = !adding_site" class="btn btn-primary">Create Site</div>
            </div>
        </div>

    </section>
</template>

<script>
    export default {
        created() {
            siteStore.dispatch('getSites');
        },
        data() {
            return {
                adding_site : false,
                domain: null
            }
        },
        methods: {
            saveSite: function () {
                siteStore.dispatch('createSite', {
                    domain: this.domain
                }).then(function(response) {

                });
            }
        },
        computed: {
            sites () {
                return siteStore.state.sites;
            }
        }
    }
</script>