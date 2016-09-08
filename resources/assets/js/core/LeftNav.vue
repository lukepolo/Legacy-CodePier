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
            <input v-model="form.domain" type="text">
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
                form : {
                    domain: null
                }
            }
        },
        methods: {
            saveSite: function () {
                siteStore.dispatch('createSite', this.form);
                this.adding_site = false;
            }
        },
        computed: {
            sites () {
                return siteStore.state.sites;
            }
        }
    }
</script>